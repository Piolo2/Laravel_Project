<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $key = 'login|' . $request->username . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            throw ValidationException::withMessages([
                'username' => 'Too many login attempts. Please try again in ' . $minutes . ' minutes.',
            ]);
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::user();
            \Illuminate\Support\Facades\Log::info('Login Success for user: ' . $user->username . ' Role: ' . $user->role);

            $targetUrl = '';
            if ($user->role === 'admin') {
                \Illuminate\Support\Facades\Log::info('Redirecting Admin to dashboard');
                $targetUrl = '/admin/dashboard';
            } elseif ($user->role === 'resident') {
                \Illuminate\Support\Facades\Log::info('Redirecting Resident');
                $targetUrl = '/service-provider';
            } else {
                \Illuminate\Support\Facades\Log::info('Redirecting Seeker');
                $targetUrl = '/service-seeker';
            }

            return redirect()->intended($targetUrl);
        }

        RateLimiter::hit($key, 1800); // 30 minutes lockout

        \Illuminate\Support\Facades\Log::info('Login Failed for username: ' . $request->username);

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:resident,seeker'],
        ]);

        $role = \App\Models\Role::where('slug', $request->role)->first();
        $role_id = $role ? $role->id : null;

        // prepare user data array
        $userData = [
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'role_id' => $role_id,
        ];

        $user = User::create($userData);

        // make profile data array
        $profileData = [
            'user_id' => $user->id,
            'full_name' => $request->full_name,
        ];

        Profile::create($profileData);

        Auth::login($user);

        return redirect('/')->with('success', 'Registration successful!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
