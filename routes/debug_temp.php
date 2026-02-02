<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/debug-skills/{id}', function ($id) {
    $user = User::with('skills')->find($id);
    if (!$user)
        return 'User not found';

    return $user->skills->map(function ($skill) {
        return [
            'name' => $skill->name,
            'pivot_status' => $skill->pivot->availability_status,
            'pivot_raw' => $skill->pivot
        ];
    });
});
