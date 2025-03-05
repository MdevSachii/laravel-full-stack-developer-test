<?php

namespace App\Livewire\Admin;

use App\Constants\ServiceConstant;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class RegisteredCar extends Component
{
    public $isOpen = false;

    public $user;

    public $customerId;

    public $sections = [];

    public $selectedTasks = [];

    public $carId;

    public $displayJobs;

    public $searchTerm;

    public $serviceDescription;

    public function openModal($id)
    {
        $this->carId = $id;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function timeToSeconds($time)
    {
        if (empty($time)) {
            return 0;
        }

        sscanf($time, '%d:%d:%d', $hours, $minutes, $seconds);

        return $hours * 3600 + $minutes * 60 + $seconds;
    }

    public function secondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function createCarService()
    {
        $totalPrice = 0;
        $totalDurationInSeconds = 0;

        DB::beginTransaction();
        try {
            $service = new Service;
            $service->discription = $this->serviceDescription;
            $service->car_id = $this->carId;
            $service->save();

            foreach ($this->selectedTasks as $selectedTask) {
                $task = new Task;
                $task->name = $selectedTask['name'];
                $task->discription = $selectedTask['description'];
                $task->price = $selectedTask['price'];
                $task->duration = $selectedTask['duration'];
                $task->save();

                $task->services()->attach($service->id, ['status' => 'draft']);
                $totalPrice += $task['price'];
                $totalDurationInSeconds += $this->timeToSeconds($task['duration']);
            }
            $service->price = $totalPrice;
            $service->duration = $this->secondsToTime($totalDurationInSeconds);
            $service->status = 'draft';
            $service->save();

            DB::commit();

        } catch (\Exception $e) {
            Log::debug($e);
            DB::rollback();
        }

        // // Optionally, reset the selected tasks
        $this->selectedTasks = [];
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.admin.registered-car', [
            'registeredCars' => $this->user->cars,
        ]);
    }

    public function mount($id)
    {
        $this->customerId = $id;
        $this->loadCars();
        $this->sections = ServiceConstant::SECTIONS;
    }

    public function loadCars()
    {
        $this->user = User::with('cars')->find($this->customerId);
    }

    public function searchCar()
    {
        $this->user = User::with(['cars' => function ($query) {
            $query->where('cars.registration_number', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('cars.model', 'like', '%'.$this->searchTerm.'%');
        }])
            ->find($this->customerId);
    }
}
