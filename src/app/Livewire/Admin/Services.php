<?php

namespace App\Livewire\Admin;

use App\Mail\ServiceCompletedMail;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Services extends Component
{
    public $searchTerm;

    public function confirmService($serviceId)
    {
        DB::beginTransaction();

        try {
            $service = Service::find($serviceId);

            if ($service && $service->status == 'draft') {
                $service->status = 'pending';
                $service->save();

                $service->tasks()->wherePivot('status', 'draft')->updateExistingPivot(
                    $service->tasks()->pluck('id')->toArray(),
                    ['status' => 'pending']
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollback();
        }
    }

    private function isAllTasksDone($service)
    {
        return $service->tasks()->wherePivot('status', '!=', 'completed')->count() === 0;
    }

    private function getNextStatus($currentStatus)
    {
        switch ($currentStatus) {
            case 'pending':
                return 'in progress';
            case 'in progress':
                return 'completed';
            case 'completed':
                break;
            default:
                return 'pending';
        }
    }

    public function toggleTaskStatus($serviceId, $taskId, $userEmail)
    {
        $service = Service::find($serviceId);

        if ($service) {
            $task = $service->tasks()->where('task_id', $taskId)->first();

            if ($task) {
                $currentStatus = $task->pivot->status;
                $newStatus = $this->getNextStatus($currentStatus);

                $service->tasks()->updateExistingPivot($taskId, [
                    'status' => $newStatus,
                ]);
                if ($currentStatus === 'pending' && $newStatus === 'in progress' && $service->status === 'pending') {
                    $service->status = 'in progress';
                    $service->start_time = Carbon::now();
                    [$hours, $minutes, $seconds] = explode(':', $service->duration);
                    $iso8601Duration = 'PT'.$hours.'H'.$minutes.'M';
                    if ($seconds > 0) {
                        $iso8601Duration .= $seconds.'S';
                    }
                    $service->end_time = Carbon::now()->add($iso8601Duration);
                    $service->save();
                }
                if ($this->isAllTasksDone($service)) {
                    $service->status = 'completed';
                    $service->end_time = Carbon::now();
                    $service->save();

                    Mail::to($userEmail)->send(new ServiceCompletedMail($service));
                }
            }
        }
    }

    public function searchService()
    {
        return Service::with(['tasks', 'car.user'])
            ->when($this->searchTerm, function ($query, $searchTerm) {
                $query->whereHas('car', function ($query) use ($searchTerm) {
                    $query->where('registration_number', 'like', '%'.$searchTerm.'%')
                        ->orWhereHas('user', function ($query) use ($searchTerm) {
                            $query->where('name', 'like', '%'.$searchTerm.'%');
                        });
                });
            })
            ->paginate(10);
    }

    public function render()
    {
        if (empty($this->searchTerm) && ! $this->searchService()) {
            $services = Service::with(['tasks', 'car.user'])->paginate(10);
        } else {
            $services = $this->searchService();
        }

        return view('livewire.admin.services', [
            'services' => $services,
        ]);
    }
}
