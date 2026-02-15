@extends('layouts.app')

@section('title', 'Register | Uni-Serve')

@section('content')
    <div class="auth-page">
        <div class="auth-split">
            <!-- Left Side: Branding/Image -->
            <div class="auth-image-side">
                <h2>Join Uni-Serve Today</h2>
                <p>Create an account to start connecting with your local community. Whether you're looking for help or
                    offering your skills, we've got you covered.</p>
                <div style="margin-top: 40px;">
                    <i class="fas fa-user-plus" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="auth-form-side">
                <h3>Register</h3>
                <p>Fill in your details to get started.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {!! session('success') !!}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" name="full_name" id="full_name" required placeholder="e.g. Juan Dela Cruz"
                            value="{{ old('full_name') }}">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" required placeholder="e.g. juan@example.com"
                            value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" required placeholder="Choose a username"
                            value="{{ old('username') }}">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required placeholder="Create a password">
                    </div>

                    <div class="form-group">
                        <label for="role">I am a...</label>
                        <select name="role" id="role">
                            <option value="resident" {{ old('role') == 'resident' ? 'selected' : '' }}>Service Provider
                                (Offers Skills/Services)</option>
                            <option value="seeker" {{ old('role') == 'seeker' ? 'selected' : '' }}>Service Seeker (Looking for
                                Help)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-accent btn-floating" style="margin-top: 10px;">Register</button>
                </form>

                <p style="text-align: center; margin-top: 25px; font-size: 0.95rem; color: #666;">
                    Already have an account? <a href="{{ route('login') }}"
                        style="color: var(--accent-blue); font-weight: 700;">Login
                        here</a>
                </p>
            </div>
        </div>
    </div>
@endsection


