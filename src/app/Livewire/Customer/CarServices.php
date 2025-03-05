<?php

namespace App\Livewire\Customer;

use App\Models\Service;
use Livewire\Component;

class CarServices extends Component
{
    public $carId;

    public function mount($id)
    {
        $this->carId = $id;
    }

    public function getService()
    {
        return Service::with(['tasks', 'car.user'])
            ->where('car_id', $this->carId)
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.customer.car-services',
            [
                'services' => $this->getService(),
            ]);
    }
}
