<?php

namespace App\Observers;

use App\Models\Student;

class StudentObserver
{
    public function creating(Student $student): void
    {
        if (! $student->admission_no) {
            $student->admission_no = $this->nextAdmissionNumber();
        }

        $student->admission_number ??= $student->admission_no;
        $student->school_class_id ??= $student->class_id;
        $student->date_of_birth ??= $student->dob;
        $student->address ??= $student->present_address;
        $student->image ??= $student->photo;
    }

    public function saving(Student $student): void
    {
        $student->admission_number = $student->admission_no ?: $student->admission_number;
        $student->school_class_id = $student->class_id ?: $student->school_class_id;
        $student->date_of_birth = $student->dob ?: $student->date_of_birth;
        $student->address = $student->present_address ?: $student->address;
        $student->image = $student->photo ?: $student->image;
    }

    private function nextAdmissionNumber(): string
    {
        $year = now()->format('Y');
        $latest = Student::withTrashed()
            ->where('admission_no', 'like', "ADM-{$year}-%")
            ->orderByDesc('admission_no')
            ->value('admission_no');

        $sequence = $latest
            ? ((int) str($latest)->afterLast('-')->toString()) + 1
            : 1;

        return 'ADM-'.$year.'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
