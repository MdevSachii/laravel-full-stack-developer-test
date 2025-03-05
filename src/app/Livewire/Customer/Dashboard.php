<?php

namespace App\Livewire\Customer;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $services = [];

    public function mount()
    {
        $this->loadRunningServices();
    }

    public function loadRunningServices()
    {
        $user = Auth::user();
        $this->services = Service::with(['tasks', 'car.user'])
            ->whereHas('car.user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->whereDate('start_time', Carbon::today())
            ->where('status', 'in progress')
            ->get()
            ->map(function ($service) {
                $totalTasks = $service->tasks->count();
                $completedTasks = $service->tasks->where('pivot.status', 'completed')->count();
                $service->progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                return $service;
            });
    }

    public function render()
    {
        return view('livewire.customer.dashboard', [
            'services' => $this->services,
        ]);
    }
}
