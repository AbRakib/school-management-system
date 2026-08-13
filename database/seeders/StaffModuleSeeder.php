<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\ClassTeacherSubject;
use App\Models\Department;
use App\Models\Designation;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffModuleSeeder extends Seeder
{
    public function run(): void
    {
        $departments = collect(['Science', 'Arts', 'Commerce', 'Administration'])
            ->map(fn (string $name) => Department::query()->updateOrCreate(['name' => $name]));

        $designations = collect([
            ['name' => 'Principal', 'is_teaching_staff' => true],
            ['name' => 'Vice Principal', 'is_teaching_staff' => true],
            ['name' => 'Senior Teacher', 'is_teaching_staff' => true],
            ['name' => 'Teacher', 'is_teaching_staff' => true],
            ['name' => 'Accountant', 'is_teaching_staff' => false],
            ['name' => 'Librarian', 'is_teaching_staff' => false],
        ])->map(fn (array $designation) => Designation::query()->updateOrCreate(
            ['name' => $designation['name']],
            ['is_teaching_staff' => $designation['is_teaching_staff']],
        ));

        Role::findOrCreate(SchoolRoles::TEACHER);
        Role::findOrCreate(SchoolRoles::STAFF);

        foreach (range(1, 15) as $index) {
            $designation = $designations->random();
            $email = "staff{$index}@school.test";
            $user = User::query()->updateOrCreate([
                'email' => $email,
            ], [
                'name' => fake()->name(),
                'password' => Hash::make('password'),
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->syncRoles([$designation->is_teaching_staff ? SchoolRoles::TEACHER : SchoolRoles::STAFF]);

            Staff::query()->updateOrCreate([
                'email' => $email,
            ], [
                'staff_id' => 'STF'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'department_id' => $departments->random()->id,
                'designation_id' => $designation->id,
                'name' => $user->name,
                'phone' => '01730'.str_pad((string) $index, 6, '0', STR_PAD_LEFT),
                'dob' => now()->subYears(fake()->numberBetween(25, 55))->toDateString(),
                'gender' => fake()->randomElement(['male', 'female', 'other']),
                'joining_date' => now()->subYears(fake()->numberBetween(1, 12))->toDateString(),
                'qualification' => fake()->randomElement(['B.Ed, M.Ed', 'M.Sc', 'BBA, MBA', 'BA Honors', null]),
                'address' => fake()->address(),
                'salary' => fake()->numberBetween(25000, 85000),
                'status' => 'active',
            ]);
        }

        $session = AcademicSession::query()->first();
        $teachingStaff = Staff::query()->teachers()->take(10)->get();
        $classes = SchoolClass::query()->get();
        $subjects = Subject::query()->get();

        if (! $session || $teachingStaff->isEmpty() || $classes->isEmpty() || $subjects->isEmpty()) {
            return;
        }

        $used = [];
        foreach ($teachingStaff as $staff) {
            foreach (range(1, 2) as $unused) {
                $class = $classes->random();
                $section = Section::query()->where('school_class_id', $class->id)->inRandomOrder()->first();
                $subject = $subjects->random();

                if (! $section) {
                    continue;
                }

                $key = "{$class->id}-{$section->id}-{$subject->id}-{$session->id}";
                if (isset($used[$key])) {
                    continue;
                }
                $used[$key] = true;

                ClassTeacherSubject::query()->updateOrCreate([
                    'class_id' => $class->id,
                    'section_id' => $section->id,
                    'subject_id' => $subject->id,
                    'academic_session_id' => $session->id,
                ], [
                    'staff_id' => $staff->id,
                ]);
            }
        }
    }
}
