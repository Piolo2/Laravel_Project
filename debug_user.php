<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$username = 'admin'; // Hardcoded for test
// You might want to ask the user for the password they are using to test against that,
// currently I will check if the user exists and what their hash looks like (safely).

$user = User::where('username', $username)->first();

if (!$user) {
    echo "User found: NO\n";
} else {
    echo "User found: YES (ID: " . $user->id . ")\n";
    echo "Role: " . $user->role . "\n";
    // We cannot check password without the input, but we can verify the user is retrievable.
}
