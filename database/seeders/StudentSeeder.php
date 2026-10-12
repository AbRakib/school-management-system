<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Seed students, guardians, sections, sessions, and categories.
     */
    public function run(): void
    {
        $faker = fake();

        $session = AcademicSession::query()->updateOrCreate([
            'name' => '2025-2026',
        ], [
            'start_date' => now()->startOfYear()->toDateString(),
            'end_date' => now()->endOfYear()->toDateString(),
            'status' => 'active',
        ]);

        $categories = collect(['General', 'Scholarship', 'Special Needs'])
            ->map(fn (string $name) => StudentCategory::query()->updateOrCreate(['name' => $name]));

        $classes = SchoolClass::query()->orderBy('grade_level')->get();
        $sections = collect();

        foreach ($classes as $schoolClass) {
            $sectionNames = $schoolClass->grade_level === 10
                ? ['Science', 'Commerce', 'Arts']
                : ['A'];

            foreach ($sectionNames as $sectionName) {
                $sections->push(Section::query()->updateOrCreate([
                    'school_class_id' => $schoolClass->id,
                    'name' => $sectionName,
                ]));
            }
        }

        foreach (range(1, 50) as $index) {
            $schoolClass = $classes[($index - 1) % max($classes->count(), 1)];
            $classSections = $sections
                ->where('school_class_id', $schoolClass->id)
                ->values();
            $section = $classSections[($index - 1) % max($classSections->count(), 1)];
            $gender = $faker->randomElement(['male', 'female', 'other']);
            $relation = $faker->randomElement(['father', 'mother', 'guardian']);
            $admissionNo = 'ADM-'.now()->format('Y').'-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT);
            $dob = now()->subYears($faker->numberBetween(6, 16))->subDays($faker->numberBetween(1, 300))->toDateString();
            $address = $faker->address();

            $guardian = Guardian::query()->updateOrCreate([
                'phone' => '01820'.str_pad((string) $index, 6, '0', STR_PAD_LEFT),
            ], [
                'name' => $faker->name($relation === 'mother' ? 'female' : 'male'),
                'relationship' => ucfirst($relation),
                'relation' => $relation,
                'email' => "guardian{$index}@school.test",
                'occupation' => $faker->jobTitle(),
                'address' => $faker->address(),
            ]);

            Student::query()->updateOrCreate([
                'admission_no' => $admissionNo,
            ], [
                'admission_number' => $admissionNo,
                'class_id' => $schoolClass->id,
                'school_class_id' => $schoolClass->id,
                'section_id' => $section->id,
                'academic_session_id' => $session->id,
                'roll_no' => (string) $index,
                'guardian_id' => $guardian->id,
                'student_category_id' => $categories->random()->id,
                'name' => $faker->name($gender === 'female' ? 'female' : 'male'),
                'dob' => $dob,
                'date_of_birth' => $dob,
                'gender' => $gender,
                'blood_group' => $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-', null]),
                'religion' => $faker->randomElement(['Islam', 'Hinduism', 'Christianity', 'Buddhism', null]),
                'present_address' => $address,
                'permanent_address' => $faker->address(),
                'address' => $address,
                'admission_date' => now()->subDays($faker->numberBetween(1, 365))->toDateString(),
                'status' => 'active',
            ]);
        }
    }
}
