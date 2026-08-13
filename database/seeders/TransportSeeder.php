<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Student;
use App\Models\StudentTransport;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = collect([
            ['vehicle_number' => 'BUS-1001', 'type' => 'Bus', 'capacity' => 45, 'driver_name' => 'Abdul Karim', 'driver_phone' => '01710001001', 'status' => 'active'],
            ['vehicle_number' => 'BUS-1002', 'type' => 'Bus', 'capacity' => 42, 'driver_name' => 'Mizan Rahman', 'driver_phone' => '01710001002', 'status' => 'active'],
            ['vehicle_number' => 'VAN-2001', 'type' => 'Van', 'capacity' => 16, 'driver_name' => 'Selim Ahmed', 'driver_phone' => '01710002001', 'status' => 'active'],
        ])->map(fn ($vehicle) => Vehicle::query()->updateOrCreate([
            'vehicle_number' => $vehicle['vehicle_number'],
        ], $vehicle));

        $routeData = [
            [
                'name' => 'Route A - Downtown',
                'vehicle' => 'BUS-1001',
                'fare_amount' => 1500,
                'stops' => ['Central Park', 'City Hall', 'Market Road', 'School Gate'],
            ],
            [
                'name' => 'Route B - North End',
                'vehicle' => 'BUS-1002',
                'fare_amount' => 1800,
                'stops' => ['North Bridge', 'Lake View', 'College More', 'School Gate'],
            ],
            [
                'name' => 'Route C - West Side',
                'vehicle' => 'VAN-2001',
                'fare_amount' => 1200,
                'stops' => ['West Colony', 'Station Road', 'Library Corner', 'Hospital Gate', 'School Gate'],
            ],
        ];

        foreach ($routeData as $index => $data) {
            $vehicle = $vehicles->firstWhere('vehicle_number', $data['vehicle']);
            $route = TransportRoute::query()->updateOrCreate([
                'name' => $data['name'],
            ], [
                'vehicle_id' => $vehicle->id,
                'fare_amount' => $data['fare_amount'],
            ]);

            $route->stops()->delete();
            foreach ($data['stops'] as $stopIndex => $stop) {
                $route->stops()->create([
                    'name' => $stop,
                    'pickup_time' => now()->setTime(7, 15)->addMinutes(($index * 10) + ($stopIndex * 12))->format('H:i'),
                    'order' => $stopIndex + 1,
                ]);
            }
        }

        $session = AcademicSession::query()->where('status', 'active')->first()
            ?? AcademicSession::query()->latest()->first();

        if (! $session || StudentTransport::query()->where('academic_session_id', $session->id)->exists()) {
            return;
        }

        $routes = TransportRoute::query()->with('stops')->get();
        Student::query()
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(20)
            ->get()
            ->each(function (Student $student) use ($routes, $session) {
                $route = $routes->random();
                $stop = $route->stops->random();

                StudentTransport::query()->updateOrCreate([
                    'student_id' => $student->id,
                    'academic_session_id' => $session->id,
                ], [
                    'transport_route_id' => $route->id,
                    'transport_stop_id' => $stop->id,
                ]);
            });
    }
}
