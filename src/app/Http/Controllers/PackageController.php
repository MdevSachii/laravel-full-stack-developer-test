<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return response()->json($packages);
    }

    public function availableSeat($id)
    {
        $package = Package::available()->get();
        return response()->json($package);
    }
}
