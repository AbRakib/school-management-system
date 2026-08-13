<?php

namespace App\Http\Controllers;

use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TransportRouteController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        return Inertia::render('transport/Routes/Index', [
            'routes' => TransportRoute::query()
                ->with(['vehicle:id,vehicle_number,type', 'stops'])
                ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->latest()
                ->get(),
            'vehicles' => Vehicle::query()->where('status', 'active')->orderBy('vehicle_number')->get(),
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $route = TransportRoute::query()->create($data['route']);
            $this->syncStops($route, $data['stops']);
        });

        return back();
    }

    public function update(Request $request, TransportRoute $transportRoute): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($transportRoute, $data) {
            $transportRoute->update($data['route']);
            $transportRoute->stops()->delete();
            $this->syncStops($transportRoute, $data['stops']);
        });

        return back();
    }

    public function destroy(TransportRoute $transportRoute): RedirectResponse
    {
        $transportRoute->delete();

        return back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'fare_amount' => ['required', 'numeric', 'min:0'],
            'stops' => ['nullable', 'array'],
            'stops.*.name' => ['required_with:stops', 'string', 'max:255'],
            'stops.*.pickup_time' => ['nullable', 'date_format:H:i'],
            'stops.*.order' => ['nullable', 'integer', 'min:1'],
        ]);

        return [
            'route' => [
                'name' => $data['name'],
                'vehicle_id' => $data['vehicle_id'],
                'fare_amount' => $data['fare_amount'],
            ],
            'stops' => collect($data['stops'] ?? [])
                ->filter(fn ($stop) => filled($stop['name'] ?? null))
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  list<array{name: string, pickup_time?: string|null, order?: int|null}>  $stops
     */
    private function syncStops(TransportRoute $route, array $stops): void
    {
        foreach ($stops as $index => $stop) {
            $route->stops()->create([
                'name' => $stop['name'],
                'pickup_time' => $stop['pickup_time'] ?? null,
                'order' => $stop['order'] ?? $index + 1,
            ]);
        }
    }
}
