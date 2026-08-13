<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Support\SchoolRoles;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return to_route(SchoolRoles::dashboardRouteNameFor(request()->user()));
    }

    public function admin(): Response
    {
        return Inertia::render('Dashboard', [
            'roleDashboard' => 'admin',
            'stats' => [
                'students' => Student::query()->count(),
                'teachers' => Teacher::query()->count(),
                'staff' => Teacher::query()->count(),
                'parents' => Guardian::query()->count(),
                'classes' => SchoolClass::query()->count(),
                'sections' => SchoolClass::query()->whereNotNull('section')->distinct('section')->count('section'),
                'newAdmissionsThisMonth' => Student::query()
                    ->whereBetween('admission_date', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'activeNotices' => Notice::query()->visible()->count(),
            ],
            'classes' => SchoolClass::query()
                ->with('classTeacher:id,name')
                ->withCount('students')
                ->orderBy('grade_level')
                ->get(['id', 'name', 'grade_level', 'section', 'class_teacher_id']),
            'recentStudents' => Student::query()
                ->with(['schoolClass:id,name', 'guardian:id,name'])
                ->latest()
                ->limit(8)
                ->get(['id', 'admission_no', 'admission_number', 'name', 'class_id', 'school_class_id', 'guardian_id', 'admission_date']),
            'latestNotices' => $this->latestNotices(),
        ]);
    }

    public function teacher(): Response
    {
        return Inertia::render('Dashboard', [
            'roleDashboard' => 'teacher',
            'stats' => $this->emptyStats(),
            'classes' => [],
            'recentStudents' => [],
            'latestNotices' => $this->latestNotices(),
        ]);
    }

    public function student(): Response
    {
        return Inertia::render('Dashboard', [
            'roleDashboard' => 'student',
            'stats' => $this->emptyStats(),
            'classes' => [],
            'recentStudents' => [],
            'latestNotices' => $this->latestNotices(),
        ]);
    }

    public function parent(): Response
    {
        return Inertia::render('Dashboard', [
            'roleDashboard' => 'parent',
            'stats' => $this->emptyStats(),
            'classes' => [],
            'recentStudents' => [],
            'latestNotices' => $this->latestNotices(),
        ]);
    }

    public function accountant(): Response
    {
        return Inertia::render('Dashboard', [
            'roleDashboard' => 'accountant',
            'stats' => $this->emptyStats(),
            'classes' => [],
            'recentStudents' => [],
            'latestNotices' => $this->latestNotices(),
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function emptyStats(): array
    {
        return [
            'students' => 0,
            'teachers' => 0,
            'staff' => 0,
            'parents' => 0,
            'classes' => 0,
            'sections' => 0,
            'newAdmissionsThisMonth' => 0,
            'activeNotices' => 0,
        ];
    }

    private function latestNotices(): mixed
    {
        return Notice::query()
            ->visible()
            ->forRole(request()->user())
            ->latest('publish_date')
            ->limit(5)
            ->get(['id', 'title', 'audience', 'publish_date', 'expiry_date']);
    }
}
