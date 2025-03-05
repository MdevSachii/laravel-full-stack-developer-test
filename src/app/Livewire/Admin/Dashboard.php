<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $services = [];

    public function mount()
    {
        $this->loadProcessingCars();
    }

    public function loadProcessingCars()
    {
        $this->services = Service::with(['tasks', 'car'])
            ->whereDate('start_time', Carbon::today())
            ->where('status', 'in progress')
            ->get()
            ->map(function ($service) {
                $totalTasks = $service->tasks->count();
                $completedTasks = $service->tasks->where('pivot.status', 'completed')->count();
                $service->progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                $service->estimated_finish_time = Carbon::parse($service->start_time)->addSeconds($this->getDurationInSeconds($service->duration));

                return $service;
            });
    }

    private function getDurationInSeconds($duration)
    {
        $parts = explode(':', $duration);
        $hours = isset($parts[0]) ? (int) $parts[0] : 0;
        $minutes = isset($parts[1]) ? (int) $parts[1] : 0;
        $seconds = isset($parts[2]) ? (int) $parts[2] : 0;

        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'services' => $this->services,
        ]);
    }
}
