<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Resident', 'slug' => 'resident'],
            ['name' => 'Service Seeker', 'slug' => 'seeker'],
            ['name' => 'Service Provider', 'slug' => 'provider'],
        ];

        foreach ($roles as $roleData) {
            $role = \App\Models\Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);

            // Assign this role_id to existing users with matching role string
            \App\Models\User::where('role', $roleData['slug'])->update(['role_id' => $role->id]);
        }
    }
}
