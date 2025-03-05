<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="Auto Shine Service",
 *     version="0.0.1",
 *     description="API Documentation for Auto Shine Service",
 *
 *     @OA\Contact(
 *         email="sachini@thesanmark.com"
 *     )
 * )
 */
class ServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/services/{id}/progress",
     *     summary="Get service progress and status",
     *     tags={"Service"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response="200", description="Service progress and status"),
     *     @OA\Response(response="404", description="Service not found"),
     *     @OA\Response(response="401", description="Unauthorized")
     * )
     */
    public function getServiceProgress($id)
    {
        $service = Service::where('id', $id)
            ->whereHas('car.user', function ($query) {
                $query->where('id', Auth::id());
            })
            ->first();

        if (! $service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        return response()->json([
            'status' => $service->status,
            'progress' => $this->calculateProgress($service),
        ]);
    }

    private function calculateProgress(Service $service)
    {
        $totalTasks = $service->tasks()->count();
        $completedTasks = $service->tasks()->wherePivot('status', 'done')->count();

        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    }

    /**
     * @OA\Get(
     *     path="/api/cars/{registration_number}/services",
     *     summary="Get all service records by car registration number",
     *     tags={"Service"},
     *     security={{ "bearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="registration_number",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(response="200", description="List of services"),
     *     @OA\Response(response="404", description="Car or services not found"),
     *     @OA\Response(response="401", description="Unauthorized")
     * )
     */
    public function getServiceRecordsByRegistration($registration_number)
    {
        $car = Car::where('registration_number', $registration_number)
            ->where('user_id', Auth::id())
            ->first();

        if (! $car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $services = $car->services()->with('tasks')->get();

        return response()->json($services);
    }
}
