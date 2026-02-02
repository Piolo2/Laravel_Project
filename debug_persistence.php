<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = 2; // Assuming ID 2 based on context
// Get a random skill for this user
$skill = \Illuminate\Support\Facades\DB::table('user_skills')->where('user_id', $userId)->first();

if (!$skill) {
    echo "No skills found for user $userId\n";
    exit;
}

echo "Initial Status for Skill ID {$skill->skill_id} (Pivot ID: {$skill->id}): [{$skill->availability_status}]\n";

// Toggle it
$newStatus = ($skill->availability_status === 'Available') ? 'Unavailable' : 'Available';
echo "Updating to: [$newStatus]...\n";

\Illuminate\Support\Facades\DB::table('user_skills')
    ->where('id', $skill->id)
    ->update(['availability_status' => $newStatus]);

// Read it back immediately
$updatedSkill = \Illuminate\Support\Facades\DB::table('user_skills')->where('id', $skill->id)->first();
echo "Readback Status: [{$updatedSkill->availability_status}]\n";

if ($updatedSkill->availability_status !== $newStatus) {
    echo "ERROR: Update failed verification!\n";
} else {
    echo "SUCCESS: Database updated verify OK.\n";
}
