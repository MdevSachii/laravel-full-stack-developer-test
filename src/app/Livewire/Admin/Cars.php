<?php

namespace App\Livewire\Admin;

use App\Models\Car;
use App\Models\User;
use Livewire\Component;

class Cars extends Component
{
    public $isOpen = false;

    public $isOpenEditModal = false;

    public $registrationNumber;

    public $fuelType;

    public $model;

    public $transmission;

    public $owner;

    public $customers;

    public $customerSelect;

    public $searchTerm;

    public function openEditModal($id)
    {
        $car = Car::with('user')->find($id);
        $this->registrationNumber = $car->registration_number;
        $this->fuelType = $car->fuel_type;
        $this->model = $car->model;
        $this->transmission = $car->transmission;
        $this->customerSelect = $car->user['id'];
        $this->isOpenEditModal = true;
    }

    public function closeEditModal()
    {
        $this->isOpenEditModal = false;
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function upadateCar($id) {}

    public function mount()
    {
        $this->customers = User::where('isadmin', false)->get();
    }

    public function render()
    {
        if (empty($this->searchTerm) && ! $this->searchCar()) {
            $cars = Car::with('user')->paginate(10);
        } else {
            $cars = $this->searchCar();
        }

        return view('livewire.admin.cars',
            [
                'cars' => $cars,
            ]);
    }

    public function deleteCar($id)
    {
        $car = Car::find($id);

        if ($car) {
            $car->delete();
        }
    }

    public function createCar()
    {

        $car = new Car;
        $car->user_id = $this->customerSelect;
        $car->registration_number = $this->registrationNumber;
        $car->model = $this->model;
        $car->fuel_type = $this->fuelType;
        $car->transmission = $this->transmission;
        $car->save();
    }

    public function searchCar()
    {
        return Car::query()
            ->join('users', 'cars.user_id', '=', 'users.id')
            ->when($this->searchTerm, function ($query, $searchTerm) {
                $query->where('cars.registration_number', 'like', '%'.$searchTerm.'%')
                    ->orWhere('cars.model', 'like', '%'.$searchTerm.'%')
                    ->orWhere('users.name', 'like', '%'.$searchTerm.'%');
            })
            ->select('cars.*')
            ->paginate(10);
    }
}
