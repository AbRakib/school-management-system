<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        return Inertia::render('transport/Vehicles/Index', [
            'vehicles' => Vehicle::query()
                ->when($search, fn ($q) => $q->where(fn ($q) => $q
                    ->where('vehicle_number', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")))
                ->latest()
                ->get(),
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Vehicle::query()->create($this->validated($request));

        return back();
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($this->validated($request, $vehicle));

        return back();
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return back();
    }

    private function validated(Request $request, ?Vehicle $vehicle = null): array
    {
        return $request->validate([
            'vehicle_number' => ['required', 'string', 'max:100', Rule::unique('vehicles')->ignore($vehicle)],
            'type' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['required', 'string', 'max:30'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
