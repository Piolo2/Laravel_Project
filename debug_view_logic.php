<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = 2; // "Piolo"
$user = \App\Models\User::find($userId);

echo "Testing Filtering Logic for User: {$user->username}\n";

// 1. Get raw skills
$allSkills = $user->skills;
echo "Total Skills: " . $allSkills->count() . "\n";
foreach ($allSkills as $s) {
    echo " - {$s->name}: [{$s->pivot->availability_status}]\n";
}

// 2. Apply the Controller Filter Logic
$filteredSkills = $user->skills->filter(function ($skill) {
    return $skill->pivot->availability_status === 'Available';
})->pluck('name');

echo "\nFiltered Skills (Logic from Controller):\n";
echo "Count: " . $filteredSkills->count() . "\n";
foreach ($filteredSkills as $name) {
    echo " - $name\n";
}

// 3. Verify correctness
$unavailableCount = $allSkills->where('pivot.availability_status', '!=', 'Available')->count();
$expectedCount = $allSkills->count() - $unavailableCount;

if ($filteredSkills->count() === $expectedCount) {
    echo "\nSUCCESS: Filtering logic works correctly.\n";
} else {
    echo "\nERROR: Logic mismatch. Expected $expectedCount, got " . $filteredSkills->count() . "\n";
}
