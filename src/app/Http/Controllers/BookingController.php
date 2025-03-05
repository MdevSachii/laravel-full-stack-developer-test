<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function bookPackage(Request $request)
    {
        $user = Auth::user();
        $packageId = $request->package_id;

        $package = Package::find($packageId);
        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }

        if ($user->packages()->wherePivot('package_id', $packageId)->exists()) {
            return response()->json(['error' => 'You have already booked this package'], 400);
        }

        if ($package->available_seats <= 0) {
            return response()->json(['error' => 'This package is fully booked'], 400);
        }

        $user->packages()->attach($packageId);

        $package->update(['available_seats' => $package->available_seats - 1]);

        return response()->json([
            'message' => 'Booking successful',
            'available_seats' => $package->available_seats
        ]);
    }
}
