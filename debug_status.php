<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();


$profile = \App\Models\Profile::where('full_name', 'LIKE', '%Piolo%')->first();
if (!$profile) {
    echo "Profile 'Piolo' not found. Listing all profiles:\n";
    foreach (\App\Models\Profile::all() as $p) {
        echo $p->id . ": " . $p->full_name . " (User " . $p->user_id . ")\n";
    }
    exit;
}

$user = $profile->user;
echo "Found User: " . $user->username . " (ID: " . $user->id . ")\n";

echo "--- Skills via Eloquent ---\n";
foreach ($user->skills as $skill) {
    echo "Skill: " . $skill->name . "\n";
    echo "  Pivot Status: [" . $skill->pivot->availability_status . "]\n";
}

echo "--- Raw DB Rows (user_skills) ---\n";
$rows = \Illuminate\Support\Facades\DB::table('user_skills')->where('user_id', $user->id)->get();
foreach ($rows as $row) {
    echo "Skill ID: " . $row->skill_id . " | Status: [" . $row->availability_status . "]\n";
}

