<?php

use App\Livewire\Admin\Cars;
use App\Livewire\Admin\Customer;
use App\Livewire\Admin\Services;
use App\Livewire\Admin\ViewServices;
use App\Livewire\Admin\RegisteredCar;
use Illuminate\Support\Facades\Route;
use App\Livewire\Customer\CarServices;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Customer\Cars as CustomerCars;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Customer\Dashboard as CustomerDashboard;


Route::view('/login', 'auth.login');
Route::view('/register', 'auth.register');

Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');

Route::view('/packages', 'packages.index');


require __DIR__.'/auth.php';
