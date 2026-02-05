<?php

use App\Models\Announcement;
use Carbon\Carbon;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Cleanup test data
Announcement::where('title', 'LIKE', 'TEST_EXPIRY_%')->delete();

// Create test announcements
$yesterday = Announcement::create([
    'title' => 'TEST_EXPIRY_YESTERDAY',
    'image_path' => 'test.jpg',
    'admin_name' => 'Admin',
    'description' => 'Test',
    'date_posted' => now()->subDays(5),
    'deadline' => now()->subDay()->toDateString(),
]);

$today = Announcement::create([
    'title' => 'TEST_EXPIRY_TODAY',
    'image_path' => 'test.jpg',
    'admin_name' => 'Admin',
    'description' => 'Test',
    'date_posted' => now()->subDays(5),
    'deadline' => now()->toDateString(),
]);

$tomorrow = Announcement::create([
    'title' => 'TEST_EXPIRY_TOMORROW',
    'image_path' => 'test.jpg',
    'admin_name' => 'Admin',
    'description' => 'Test',
    'date_posted' => now()->subDays(5),
    'deadline' => now()->addDay()->toDateString(),
]);

echo "Created test announcements:\n";
echo "Yesterday: " . $yesterday->deadline . "\n";
echo "Today: " . $today->deadline . "\n";
echo "Tomorrow: " . $tomorrow->deadline . "\n";

echo "\nActive Announcements (via scopeActive):\n";
$active = Announcement::active()->get();

$foundYesterday = false;
$foundToday = false;
$foundTomorrow = false;

foreach ($active as $a) {
    if ($a->title === 'TEST_EXPIRY_YESTERDAY')
        $foundYesterday = true;
    if ($a->title === 'TEST_EXPIRY_TODAY')
        $foundToday = true;
    if ($a->title === 'TEST_EXPIRY_TOMORROW')
        $foundTomorrow = true;

    if (strpos($a->title, 'TEST_EXPIRY_') !== false) {
        echo "- " . $a->title . " (Deadline: " . $a->deadline . ")\n";
    }
}

echo "\nResults:\n";
echo "Yesterday (Should be FALSE): " . ($foundYesterday ? "FOUND (FAIL)" : "NOT FOUND (PASS)") . "\n";
echo "Today (Should be FALSE): " . ($foundToday ? "FOUND (FAIL)" : "NOT FOUND (PASS)") . "\n";
echo "Tomorrow (Should be TRUE): " . ($foundTomorrow ? "FOUND (PASS)" : "NOT FOUND (FAIL)") . "\n";

// Cleanup
Announcement::where('title', 'LIKE', 'TEST_EXPIRY_%')->delete();
