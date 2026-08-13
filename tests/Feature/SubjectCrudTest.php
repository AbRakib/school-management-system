<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubjectCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_subjects(): void
    {
        $response = $this->get(route('subjects.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_create_update_and_delete_subjects(): void
    {
        $user = User::factory()->create();
        Role::findOrCreate(SchoolRoles::ADMIN);
        $user->assignRole(SchoolRoles::ADMIN);

        $this->actingAs($user);

        $teacher = Teacher::query()->create([
            'name' => 'Science Teacher',
            'designation' => 'teacher',
        ]);

        $class = SchoolClass::query()->create([
            'grade_level' => 6,
            'name' => 'Class 6',
        ]);

        $this->post(route('subjects.store'), [
            'name' => 'General Science',
            'code' => 'SCI-6',
            'school_class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'description' => 'Core science subject for Class 6.',
        ])->assertRedirect(route('subjects.index', absolute: false));

        $subject = Subject::query()->firstOrFail();

        $this->assertSame('General Science', $subject->name);
        $this->assertSame('SCI-6', $subject->code);
        $this->assertSame($class->id, $subject->school_class_id);
        $this->assertSame($teacher->id, $subject->teacher_id);

        $this->put(route('subjects.update', $subject), [
            'name' => 'Science',
            'code' => 'SCI-06',
            'school_class_id' => null,
            'teacher_id' => null,
            'description' => null,
        ])->assertRedirect(route('subjects.index', absolute: false));

        $subject->refresh();

        $this->assertSame('Science', $subject->name);
        $this->assertSame('SCI-06', $subject->code);
        $this->assertNull($subject->school_class_id);
        $this->assertNull($subject->teacher_id);

        $this->delete(route('subjects.destroy', $subject))
            ->assertRedirect(route('subjects.index', absolute: false));

        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id,
        ]);
    }

    public function test_subject_code_must_be_unique(): void
    {
        $user = User::factory()->create();
        Role::findOrCreate(SchoolRoles::ADMIN);
        $user->assignRole(SchoolRoles::ADMIN);

        $this->actingAs($user);

        Subject::query()->create([
            'name' => 'Mathematics',
            'code' => 'MATH',
        ]);

        $response = $this->post(route('subjects.store'), [
            'name' => 'Advanced Mathematics',
            'code' => 'MATH',
        ]);

        $response->assertSessionHasErrors('code');
    }
}
