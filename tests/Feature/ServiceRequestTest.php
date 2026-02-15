<?php

use App\Models\User;
use App\Models\Role;
use App\Models\ServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

const UPDATE_REQUEST_ENDPOINT = '/requests/update';

beforeEach(function () {
    // Seed roles
    Role::forceCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
    Role::forceCreate(['id' => 2, 'name' => 'Service Provider', 'slug' => 'resident']);
    Role::forceCreate(['id' => 3, 'name' => 'Service Seeker', 'slug' => 'seeker']);
});

test('seeker can create a service request', function () {
    $seeker = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $provider = User::factory()->create(['role' => 'resident', 'role_id' => 2]);

    $response = $this->actingAs($seeker)->post('/requests/store', [
        'provider_id' => $provider->id,
        'service_date' => now()->addDay()->toDateTimeString(),
        'notes' => 'I need help with plumbing.',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('service_requests', [
        'seeker_id' => $seeker->id,
        'provider_id' => $provider->id,
        'status' => 'Pending',
    ]);
});

test('seeker cannot create request for non-resident', function () {
    $seeker = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $nonProvider = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);

    $response = $this->actingAs($seeker)->post('/requests/store', [
        'provider_id' => $nonProvider->id,
        'service_date' => now()->addDay()->toDateTimeString(),
        'notes' => 'Mistake.',
    ]);

    $response->assertSessionHasErrors('provider_id');
});

test('provider can accept a pending request', function () {
    $seeker = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $provider = User::factory()->create(['role' => 'resident', 'role_id' => 2]);

    $request = ServiceRequest::create([
        'seeker_id' => $seeker->id,
        'provider_id' => $provider->id,
        'service_date' => now()->addDay(),
        'status' => 'Pending',
        'notes' => 'Fix leak',
    ]);

    $response = $this->actingAs($provider)->post(UPDATE_REQUEST_ENDPOINT, [
        'request_id' => $request->id,
        'status' => 'Accepted',
    ]);

    $response->assertSessionHas('msg'); // Controller uses 'msg' for success flash in update
    $this->assertDatabaseHas('service_requests', [
        'id' => $request->id,
        'status' => 'Accepted',
    ]);
});

test('provider can decline a pending request', function () {
    $seeker = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $provider = User::factory()->create(['role' => 'resident', 'role_id' => 2]);

    $request = ServiceRequest::create([
        'seeker_id' => $seeker->id,
        'provider_id' => $provider->id,
        'service_date' => now()->addDay(),
        'status' => 'Pending',
        'notes' => 'Too busy',
    ]);

    $this->actingAs($provider)->post(UPDATE_REQUEST_ENDPOINT, [
        'request_id' => $request->id,
        'status' => 'Declined',
    ]);

    $this->assertDatabaseHas('service_requests', [
        'id' => $request->id,
        'status' => 'Declined',
    ]);
});

test('seeker can cancel a pending request', function () {
    $seeker = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $provider = User::factory()->create(['role' => 'resident', 'role_id' => 2]);

    $request = ServiceRequest::create([
        'seeker_id' => $seeker->id,
        'provider_id' => $provider->id,
        'service_date' => now()->addDay(),
        'status' => 'Pending',
        'notes' => 'Changed mind',
    ]);

    $this->actingAs($seeker)->post(UPDATE_REQUEST_ENDPOINT, [
        'request_id' => $request->id,
        'status' => 'Cancelled',
    ]);

    $this->assertDatabaseHas('service_requests', [
        'id' => $request->id,
        'status' => 'Cancelled',
    ]);
});

test('unauthorized user cannot update request', function () {
    $seeker = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);
    $provider = User::factory()->create(['role' => 'resident', 'role_id' => 2]);
    $otherUser = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);

    $request = ServiceRequest::create([
        'seeker_id' => $seeker->id,
        'provider_id' => $provider->id,
        'service_date' => now()->addDay(),
        'status' => 'Pending',
        'notes' => 'Secret',
    ]);

    $response = $this->actingAs($otherUser)->post(UPDATE_REQUEST_ENDPOINT, [
        'request_id' => $request->id,
        'status' => 'Cancelled',
    ]);

    $response->assertStatus(403);
});
