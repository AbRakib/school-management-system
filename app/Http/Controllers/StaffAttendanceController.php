<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StaffAttendanceController extends Controller
{
    public function create(Request $request): Response
    {
        $date = $request->query('date', now()->toDateString());
        $marked = StaffAttendance::query()
            ->where('date', $date)
            ->get()
            ->keyBy('staff_id');

        $staff = Staff::query()
            ->with(['department:id,name', 'designation:id,name'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(fn (Staff $staff) => [
                'id' => $staff->id,
                'staff_id' => $staff->staff_id,
                'name' => $staff->name,
                'photo_url' => $staff->photo_url,
                'department' => $staff->department?->name,
                'designation' => $staff->designation?->name,
                'status' => $marked[$staff->id]->status ?? 'present',
                'check_in' => $marked[$staff->id]->check_in ?? '',
                'check_out' => $marked[$staff->id]->check_out ?? '',
            ]);

        return Inertia::render('attendance/MarkStaff', [
            'staff' => $staff,
            'date' => $date,
            'statuses' => ['present', 'absent', 'late', 'on_leave'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'attendances' => ['required', 'array'],
            'attendances.*.staff_id' => ['required', 'integer', 'exists:staff,id'],
            'attendances.*.status' => ['required', Rule::in(['present', 'absent', 'late', 'on_leave'])],
            'attendances.*.check_in' => ['nullable', 'date_format:H:i'],
            'attendances.*.check_out' => ['nullable', 'date_format:H:i'],
        ]);

        DB::transaction(function () use ($data, $request) {
            foreach ($data['attendances'] as $attendance) {
                StaffAttendance::query()->updateOrCreate([
                    'staff_id' => $attendance['staff_id'],
                    'date' => $data['date'],
                ], [
                    'status' => $attendance['status'],
                    'check_in' => $attendance['check_in'] ?: null,
                    'check_out' => $attendance['check_out'] ?: null,
                    'marked_by' => $request->user()->id,
                ]);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Staff attendance saved successfully.']);

        return to_route('attendance.staff.create', ['date' => $data['date']]);
    }

    public function report(Request $request): Response
    {
        $filters = [
            'staff_id' => $request->query('staff_id'),
            'start_date' => $request->query('start_date', now()->subDays(30)->toDateString()),
            'end_date' => $request->query('end_date', now()->toDateString()),
        ];

        $rows = Staff::query()
            ->with(['department:id,name', 'designation:id,name'])
            ->when($filters['staff_id'], fn ($query, $id) => $query->where('id', $id))
            ->withCount([
                'attendances as marked_days' => fn ($query) => $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]),
                'attendances as present_days' => fn ($query) => $query->whereBetween('date', [$filters['start_date'], $filters['end_date']])->whereIn('status', ['present', 'late']),
                'attendances as absent_days' => fn ($query) => $query->whereBetween('date', [$filters['start_date'], $filters['end_date']])->where('status', 'absent'),
                'attendances as leave_days' => fn ($query) => $query->whereBetween('date', [$filters['start_date'], $filters['end_date']])->where('status', 'on_leave'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Staff $staff) => [
                'id' => $staff->id,
                'staff_id' => $staff->staff_id,
                'name' => $staff->name,
                'designation' => $staff->designation?->name,
                'marked_days' => $staff->marked_days,
                'present_days' => $staff->present_days,
                'absent_days' => $staff->absent_days,
                'leave_days' => $staff->leave_days,
                'percentage' => $staff->marked_days ? round(($staff->present_days / $staff->marked_days) * 100, 2) : 0,
            ]);

        return Inertia::render('attendance/StaffReport', [
            'rows' => $rows,
            'filters' => $filters,
            'options' => [
                'staff' => Staff::query()->orderBy('name')->get(['id', 'name', 'staff_id']),
            ],
        ]);
    }
}
