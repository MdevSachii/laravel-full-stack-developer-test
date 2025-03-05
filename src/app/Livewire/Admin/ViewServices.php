<?php

namespace App\Livewire\Admin;

use App\Models\Service;
use Livewire\Component;

class ViewServices extends Component
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
        return view('livewire.admin.view-services',
            [
                'services' => $this->getService(),
            ]);
    }
}
