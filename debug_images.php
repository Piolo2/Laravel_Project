<?php
use App\Models\Profile;
use App\Models\Accomplishment;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Profiles ---\n";
foreach (Profile::all() as $p) {
    if ($p->profile_picture) {
        echo "User {$p->user_id}: {$p->profile_picture}\n";
    }
}

echo "\n--- Accomplishments ---\n";
foreach (Accomplishment::all() as $a) {
    echo "ID {$a->id}: {$a->image_path}\n";
}
