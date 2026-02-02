<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data to avoid duplicates if re-running without fresh
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Skill::truncate();
        SkillCategory::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            'Home Repairs & Maintenance' => ['Carpenter', 'Plumber', 'Electrician', 'Painter', 'Mason / Construction Worker'],
            'Technical & Mechanical' => ['Auto Mechanic', 'Motorcycle Mechanic', 'Appliance Repair', 'Computer Technician'],
            'Personal & Household Services' => ['Laundry Service', 'House Cleaning', 'Cook / Catering', 'Tailor / Seamstress'],
            'Transportation & Logistics' => ['Trike Driver', 'Delivery Rider', 'Errand Runner'],
            'Community Wellness' => ['Massage Therapist (Hilot)', 'Barber / Hairdresser', 'Tutor'],
        ];

        foreach ($categories as $catName => $skills) {
            $category = SkillCategory::create(['name' => $catName]);
            foreach ($skills as $skillName) {
                Skill::create([
                    'category_id' => $category->id,
                    'name' => $skillName
                ]);
            }
        }
    }
}
