<?php

namespace App\Http\Controllers;

use App\Models\TimetablePeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TimetablePeriodController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('timetable/Periods/Index', [
            'periods' => TimetablePeriod::query()->orderBy('start_time')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        TimetablePeriod::query()->create($this->validated($request));
        return back();
    }

    public function update(Request $request, TimetablePeriod $period): RedirectResponse
    {
        $period->update($this->validated($request));
        return back();
    }

    public function destroy(TimetablePeriod $period): RedirectResponse
    {
        $period->delete();
        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
    }
}
