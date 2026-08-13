<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Student;
use App\Models\StudentTransport;
use App\Models\TransportRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StudentTransportController extends Controller
{
    public function index(Request $request): Response
    {
        $routeId = $request->query('route_id');
        $studentSearch = $request->query('student_search');
        $session = AcademicSession::query()
            ->where('status', 'active')
            ->first()
            ?? AcademicSession::query()->latest()->first();

        return Inertia::render('transport/Assign', [
            'routes' => TransportRoute::query()
                ->with(['vehicle:id,vehicle_number,type', 'stops'])
                ->orderBy('name')
                ->get(),
            'students' => Student::query()
                ->with(['schoolClass:id,name', 'section:id,name'])
                ->when($studentSearch, fn ($q) => $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$studentSearch}%")
                    ->orWhere('admission_no', 'like', "%{$studentSearch}%")))
                ->where('status', 'active')
                ->limit(10)
                ->get(['id', 'name', 'admission_no', 'class_id', 'section_id']),
            'assignments' => StudentTransport::query()
                ->with(['student:id,name,admission_no,class_id,section_id', 'student.schoolClass:id,name', 'student.section:id,name', 'route:id,name,fare_amount', 'stop:id,name,pickup_time'])
                ->when($routeId, fn ($q) => $q->where('transport_route_id', $routeId))
                ->when($session, fn ($q) => $q->where('academic_session_id', $session->id))
                ->latest()
                ->get(),
            'academicSession' => $session,
            'filters' => [
                'routeId' => $routeId,
                'studentSearch' => $studentSearch,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'transport_route_id' => ['required', 'exists:transport_routes,id'],
            'transport_stop_id' => [
                'required',
                Rule::exists('transport_stops', 'id')->where('transport_route_id', $request->input('transport_route_id')),
            ],
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
        ]);

        StudentTransport::query()->updateOrCreate([
            'student_id' => $data['student_id'],
            'academic_session_id' => $data['academic_session_id'],
        ], [
            'transport_route_id' => $data['transport_route_id'],
            'transport_stop_id' => $data['transport_stop_id'],
        ]);

        return back();
    }

    public function destroy(StudentTransport $studentTransport): RedirectResponse
    {
        $studentTransport->delete();

        return back();
    }
}
