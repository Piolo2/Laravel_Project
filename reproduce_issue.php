<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Starting check...\n";

try {
    $profiles = \App\Models\Profile::has('user')->with(['user.skills'])
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

    echo "Fetched " . $profiles->count() . " profiles with location.\n";

    $orphans = 0;
    foreach ($profiles as $profile) {
        if (!$profile->user) {
            echo "CRITICAL: Profile for user_id {$profile->user_id} has no User record!\n";
            $orphans++;
        }
    }

    if ($orphans > 0) {
        echo "Found $orphans orphaned profiles. This causes the SearchController to crash.\n";
    } else {
        echo "No orphaned profiles found. Issue might be elsewhere.\n";

        // Try to execute the map logic exactly as in controller to catch other errors
        $profiles->map(function ($profile) {
            $role = $profile->user->role; // check role access
            $skills = $profile->user->skills->pluck('name')->implode(', ');
            return true;
        });
        echo "Controller logic executed successfully.\n";
    }

} catch (\Throwable $e) {
    echo "Exception encountered: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
