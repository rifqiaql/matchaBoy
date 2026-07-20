@extends('layouts.guest')

@section('content')
    <div class="login-split">
        <div class="login-left">
            <div class="left-content">
                <div class="brand">
                    <span class="dot"></span>
                    <span>MatchaBoy</span>
                </div>
                <h1 class="headline">Steeped in
                    <br>tranquility.
                </h1>
                <p class="subtitle">The modern tea house experience, refined for the digital artisan.</p>
            </div>
        </div>
        <div class="login-right">
            <div class="card">
                <h2>Welcome Back</h2>
                <p class="muted">Please enter your credentials to access your portal.</p>

                <form method="POST" action="{{ route('login.store') }}" style="margin-top:18px">
                    @csrf

                    <div class="form-group">
                        <label class="small">Email Address</label>
                        <!-- REVISI: Atribut autocomplete="username" wajib ada agar browser mendeteksi ini sebagai identitas login -->
                        <input id="email" type="email" class="form-input" name="email" value="{{ old('email') }}"
                            required autocomplete="username" autofocus placeholder="name@teahouse.com">
                        @if ($errors->has('email'))
                            <div class="small" style="color:#c0392b">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="small">Password</label>
                        <!-- REVISI: Atribut autocomplete="current-password" wajib ada agar browser bisa menawarkan auto-fill password -->
                        <input id="password" type="password" class="form-input" name="password" required
                            autocomplete="current-password" placeholder="••••••••">
                        @if ($errors->has('password'))
                            <div class="small" style="color:#c0392b">{{ $errors->first('password') }}</div>
                        @endif
                        @if (Route::has('password.request'))
                            <div style="margin-top:8px;">
                                <a href="{{ route('password.request') }}" class="small" style="color:#56705a">Forgot
                                    Password?</a>
                            </div>
                        @endif
                    </div>

                    <div style="display:flex; align-items:center; gap:8px; margin-top:6px;">
                        <!-- REVISI: id="remember" ditambahkan, dan value ditahan menggunakan fungsi old() jika login gagal -->
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7a6b">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            Keep me signed in for 30 days
                        </label>
                    </div>

                    <div class="form-actions">
                        <div class="muted small">Don't have an account?
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" style="color:#365b40; font-weight:600"> Sign Up</a>
                            @endif
                        </div>
                        <button type="submit" class="btn-primary">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
