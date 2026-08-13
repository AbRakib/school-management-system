<?php

namespace App\Observers;

use App\Models\Staff;

class StaffObserver
{
    public function creating(Staff $staff): void
    {
        if (! $staff->staff_id) {
            $staff->staff_id = $this->nextStaffId();
        }
    }

    private function nextStaffId(): string
    {
        $year = now()->format('Y');
        $latest = Staff::withTrashed()
            ->where('staff_id', 'like', "STF-{$year}-%")
            ->orderByDesc('staff_id')
            ->value('staff_id');

        $sequence = $latest
            ? ((int) str($latest)->afterLast('-')->toString()) + 1
            : 1;

        return 'STF-'.$year.'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
