@extends('layouts.app')

@section('title', 'Login | Uni-Serve')

@section('content')
    <div class="auth-page">
        <div class="auth-split">
            <!-- Left Side: Branding/Image -->
            <div class="auth-image-side">
                <h2>Welcome Back to Uni-Serve</h2>
                <p>Connect with local experts, manage your services, and stay updated with your community in Unisan.</p>
                <div style="margin-top: 40px;">
                    <i class="fas fa-hand-holding-heart" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="auth-form-side">
                <h3>Login</h3>
                <p>Please enter your credentials to access your account.</p>

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" required placeholder="Your username"
                            value="{{ old('username') }}">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required placeholder="Your password">
                    </div>

                    <button type="submit" class="btn btn-accent btn-floating" style="margin-top: 20px;">Login</button>
                </form>

                <p style="text-align: center; margin-top: 30px; font-size: 0.95rem; color: #666;">
                    Don't have an account? <a href="{{ route('register') }}"
                        style="color: var(--accent-blue); font-weight: 700;">Register here</a>
                </p>
            </div>
        </div>
    </div>
@endsection