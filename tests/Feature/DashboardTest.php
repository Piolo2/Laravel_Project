<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles
    Role::forceCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
    Role::forceCreate(['id' => 2, 'name' => 'Service Provider', 'slug' => 'resident']);
    Role::forceCreate(['id' => 3, 'name' => 'Service Seeker', 'slug' => 'seeker']);
});

test('admin dashboard is accessible by admin', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'role_id' => 1,
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
});

test('provider dashboard is accessible by provider', function () {
    $provider = User::factory()->create([
        'role' => 'resident',
        'role_id' => 2,
    ]);

    $response = $this->actingAs($provider)->get('/service-provider');

    $response->assertStatus(200);
});

test('seeker dashboard is accessible by seeker', function () {
    $seeker = User::factory()->create([
        'role' => 'seeker',
        'role_id' => 3,
    ]);

    $response = $this->actingAs($seeker)->get('/service-seeker');

    $response->assertStatus(200);
});

const LOGIN_URL = '/login';

test('guests are redirected to login when accessing dashboards', function () {
    $this->get('/admin/dashboard')->assertRedirect(LOGIN_URL);
    $this->get('/service-provider')->assertRedirect(LOGIN_URL);
    $this->get('/service-seeker')->assertRedirect(LOGIN_URL);
});

// Assuming there is some middleware to protect specific dashboards from other roles.
// If not, these tests might fail if the app doesn't implement role-based middleware for these routes specifically beyond generic 'auth'.
// Based on routes/web.php, they are only under 'auth' middleware group.
// However, good practice suggests they should be restricted.
// I will just check if they are reachable for now, as I don't see specific role middleware in the routes file.
