<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! Auth::check()) {
            return Redirect::route('login');
        }

        switch (Auth::user()->isadmin) {
            case 1:
                return Redirect::route('admin.dashboard');
            case 0:
                return Redirect::route('customer.dashboard');
            default:
                return Redirect::route('login');
        }
    }
}
