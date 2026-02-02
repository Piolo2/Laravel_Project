<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            \Illuminate\Support\Facades\Log::info('Login Success for user: ' . $user->username . ' Role: ' . $user->role);

            if ($user->role === 'admin') {
                \Illuminate\Support\Facades\Log::info('Redirecting Admin to dashboard');
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role === 'resident') {
                \Illuminate\Support\Facades\Log::info('Redirecting Resident');
                return redirect()->intended('/service-provider');
            } else {
                \Illuminate\Support\Facades\Log::info('Redirecting Seeker');
                return redirect()->intended('/service-seeker');
            }
        }

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

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'role_id' => $role ? $role->id : null,
        ]);

        Profile::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
        ]);

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
