<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Customer extends Component
{
    use WithPagination;

    public $isOpen = false;

    public $name;

    public $phone;

    public $email;

    public $nic;

    public $password;

    public $area;

    public $city;

    public $state;

    public $postCode;

    public $searchTerm;

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function createCustomer()
    {
        return User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone' => $this->phone,
            'address' => $this->area.', '.$this->city.', '.$this->state.', '.$this->postCode.'.',
            'nic' => $this->nic,
        ]);
    }

    public function render()
    {
        if (empty($this->searchTerm) && ! $this->searchCustomer()) {
            $customers = $this->getCustomers();
        } else {
            $customers = $this->searchCustomer();
        }

        return view('livewire.admin.customer', [
            'customers' => $customers,
        ]);
    }

    public function getCustomers()
    {
        return User::where('isadmin', false)
            ->paginate(10);
    }

    public function searchCustomer()
    {
        return User::query()
            ->where('isadmin', false)
            ->when($this->searchTerm, function ($query, $search) {
                $query->where('email', 'like', '%'.$search.'%')
                    ->orWhere('nic', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            })
            ->paginate(10);
    }
}
