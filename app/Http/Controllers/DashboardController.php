<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'students' => Student::query()->count(),
                'teachers' => Teacher::query()->count(),
                'parents' => Guardian::query()->count(),
                'classes' => SchoolClass::query()->count(),
            ],
            'classes' => SchoolClass::query()
                ->with('classTeacher:id,name')
                ->withCount('students')
                ->orderBy('grade_level')
                ->get(['id', 'name', 'grade_level', 'section', 'class_teacher_id']),
            'recentStudents' => Student::query()
                ->with(['schoolClass:id,name', 'guardian:id,name'])
                ->latest()
                ->limit(5)
                ->get(['id', 'admission_number', 'name', 'school_class_id', 'guardian_id', 'admission_date']),
        ]);
    }
}
