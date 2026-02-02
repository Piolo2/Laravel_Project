<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = 2;
echo "Checking duplicates for User ID $userId...\n";

$duplicates = \Illuminate\Support\Facades\DB::table('user_skills')
    ->select('user_id', 'skill_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
    ->where('user_id', $userId)
    ->groupBy('user_id', 'skill_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->count() > 0) {
    echo "Found duplicates!\n";
    foreach ($duplicates as $dup) {
        echo "Skill ID: {$dup->skill_id} has {$dup->count} entries.\n";
        $entries = \Illuminate\Support\Facades\DB::table('user_skills')
            ->where('user_id', $userId)
            ->where('skill_id', $dup->skill_id)
            ->get();
        foreach ($entries as $e) {
            echo " - ID: {$e->id} | Status: [{$e->availability_status}] | Updated: {$e->updated_at}\n";
        }
    }
} else {
    echo "No duplicates found.\n";
}
