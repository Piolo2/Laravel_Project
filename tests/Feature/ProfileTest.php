<?php

use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

const PROFILE_ENDPOINT = '/profile';
const TEST_USER_NAME = 'New Name';

beforeEach(function () {
    // Seed roles
    Role::forceCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
    Role::forceCreate(['id' => 2, 'name' => 'Service Provider', 'slug' => 'resident']);
    Role::forceCreate(['id' => 3, 'name' => 'Service Seeker', 'slug' => 'seeker']);
});

test('user can view own profile page', function () {
    $user = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);

    // Profile is created by UserFactory
    $user->refresh();
    $user->profile->update(['full_name' => 'Test User']);

    $response = $this->actingAs($user)->get(PROFILE_ENDPOINT);

    $response->assertStatus(200);
    $response->assertSee('Test User');
});

test('user can update profile', function () {
    $user = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $user->refresh();
    $user->profile->update(['full_name' => 'Old Name']);

    $response = $this->actingAs($user)->post(PROFILE_ENDPOINT, [
        'full_name' => TEST_USER_NAME,
        'address' => 'Plaridel', // Must be from the approved list
        'contact_number' => '09123456789',
        'bio' => 'New Bio',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('profiles', [
        'user_id' => $user->id,
        'full_name' => TEST_USER_NAME,
        'address' => 'Plaridel, Unisan, Quezon', // Controller appends this
    ]);
});

test('profile update fails with invalid address', function () {
    $user = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $user->refresh();
    $user->profile->update(['full_name' => 'Test']);

    $response = $this->actingAs($user)->post(PROFILE_ENDPOINT, [
        'full_name' => TEST_USER_NAME,
        'address' => 'Gotham City', // Invalid
    ]);

    $response->assertSessionHasErrors('address');
});

test('can view another user profile', function () {
    $seeker = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $provider = User::factory()->create(['role' => 'resident', 'role_id' => 2]);
    $provider->refresh();
    $provider->profile->update([
        'full_name' => 'Pro Provider',
        'address' => 'Plaridel, Unisan, Quezon'
    ]);

    $response = $this->actingAs($seeker)->get("/view-profile/{$provider->id}");

    $response->assertStatus(200);
    $response->assertSee('Pro Provider');
});
