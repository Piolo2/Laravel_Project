<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles
    Role::forceCreate(['id' => 1, 'name' => 'Admin', 'slug' => 'admin']);
    Role::forceCreate(['id' => 2, 'name' => 'Service Provider', 'slug' => 'resident']);
    Role::forceCreate(['id' => 3, 'name' => 'Service Seeker', 'slug' => 'seeker']);
});

test('admin can view users list', function () {
    $admin = User::factory()->create(['role' => 'admin', 'role_id' => 1]);
    User::factory()->count(3)->create(); // Create some users

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertStatus(200);
    $response->assertViewHas('users');
});

test('admin can delete a user', function () {
    $admin = User::factory()->create(['role' => 'admin', 'role_id' => 1]);
    $userToDelete = User::factory()->create(['role' => 'seeker', 'role_id' => 3]);

    $response = $this->actingAs($admin)->delete("/admin/users/{$userToDelete->id}");

    $response->assertRedirect();
    $response->assertSessionHas('success');
    // User model uses hard delete
    // Actually User model didn't show SoftDeletes trait in previous view_file. So check DatabaseMissing.
    $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
});

test('admin can create an announcement', function () {
    $admin = User::factory()->create(['role' => 'admin', 'role_id' => 1]);

    Storage::fake('public');
    $file = UploadedFile::fake()->image('event.jpg');

    $response = $this->actingAs($admin)->post('/admin/announcements', [
        'title' => 'Community Event',
        'description' => 'Join us!',
        'date_posted' => now()->toDateString(),
        'deadline' => now()->addWeek()->toDateString(),
        'image' => $file,
    ]);

    $response->assertRedirect(route('admin.announcements.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('announcements', [
        'title' => 'Community Event',
        'description' => 'Join us!',
    ]);

    // Note: The controller manually moves files to public_path(), which Storage::fake() doesn't intercept for move().
    // So we skip asserting file existence on disk to avoid messy cleanup in public folder, relying on DB and response.
});

test('admin can delete an announcement', function () {
    $admin = User::factory()->create(['role' => 'admin', 'role_id' => 1]);

    // Create dummy announcement manually
    $announcement = Announcement::create([
        'title' => 'Old News',
        'description' => 'Remove me',
        'date_posted' => now()->subDay(),
        'deadline' => now(),
        'image_path' => 'images/announcements/dummy.jpg',
        'admin_name' => 'Admin'
    ]);

    $response = $this->actingAs($admin)->delete("/admin/announcements/{$announcement->id}");

    $response->assertRedirect(route('admin.announcements.index'));
    $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
});
