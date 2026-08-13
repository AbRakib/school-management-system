<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\SchoolRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_redirected_to_their_role_dashboard()
    {
        $user = User::factory()->create();
        Role::findOrCreate(SchoolRoles::STUDENT);
        $user->assignRole(SchoolRoles::STUDENT);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('dashboard.student', absolute: false));
    }
}
