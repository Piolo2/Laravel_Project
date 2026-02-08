<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles expected by the application
    Role::forceCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
    Role::forceCreate(['id' => 2, 'name' => 'Service Provider', 'slug' => 'resident']); // AutController checks 'resident'
    Role::forceCreate(['id' => 3, 'name' => 'Service Seeker', 'slug' => 'seeker']);
});

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'password'),
        'role' => 'seeker',
        'role_id' => 3,
    ]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => $password,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/service-seeker');
});

test('admins are redirected to admin dashboard', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'password'),
        'role' => 'admin',
        'role_id' => 1,
    ]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => $password,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/admin/dashboard');
});

test('providers are redirected to provider dashboard', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'password'),
        'role' => 'resident', // AuthController checks 'resident' for providers
        'role_id' => 2,
    ]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => $password,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/service-provider');
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'full_name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password',
        'role' => 'seeker',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');

    // Check if user and profile were created
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $this->assertDatabaseHas('profiles', ['full_name' => 'Test User']);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
