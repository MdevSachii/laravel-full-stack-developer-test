<?php

namespace App\Livewire\Customer;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Cars extends Component
{
    public function render()
    {
        return view('livewire.customer.cars',
            [
                'user' => User::with('cars')->find(Auth::user()->id),
            ]);
    }
}
