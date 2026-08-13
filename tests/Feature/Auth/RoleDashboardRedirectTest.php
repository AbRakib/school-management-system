<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleDashboardRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_users_to_their_role_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'teacher@example.test',
        ]);
        Role::findOrCreate(SchoolRoles::TEACHER);
        $user->assignRole(SchoolRoles::TEACHER);

        $response = $this->post(route('login.store'), [
            'email' => 'teacher@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard.teacher', absolute: false));
    }

    public function test_admin_resources_are_protected_by_role(): void
    {
        $student = User::factory()->create();
        Role::findOrCreate(SchoolRoles::STUDENT);
        $student->assignRole(SchoolRoles::STUDENT);

        $this->actingAs($student)
            ->get(route('subjects.index'))
            ->assertForbidden();
    }
}
