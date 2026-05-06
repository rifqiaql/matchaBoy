@extends('layouts.app')

@section('content')
<div class="login-split">
    <div class="login-left">
        <div class="left-content">
            <div class="brand">
                <span class="dot"></span>
                <span>MatchaBoy</span>
            </div>
            <h1 class="headline">Elevate your daily ritual to a moment of pure tranquility.</h1>
        </div>
    </div>
    <div class="login-right">
        <div class="card">
            <h2>Join MatchaBoy</h2>
            <p class="muted">Begin your journey to premium tea brewing</p>

            <form method="POST" action="{{ route('register') }}" style="margin-top:18px">
                @csrf

                <div class="form-group">
                    <label class="small">Full Name</label>
                    <input id="name" type="text" class="form-input" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your name">
                    @if ($errors->has('name'))
                        <div class="small" style="color:#c0392b">{{ $errors->first('name') }}</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="small">Email Address</label>
                    <input id="email" type="email" class="form-input" name="email" value="{{ old('email') }}" required placeholder="hello@matchaboy.com">
                    @if ($errors->has('email'))
                        <div class="small" style="color:#c0392b">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <div style="display: flex; gap: 12px;">
                    <div class="form-group" style="flex: 1;">
                        <label class="small">Password</label>
                        <input id="password" type="password" class="form-input" name="password" required placeholder="••••••••">
                        @if ($errors->has('password'))
                            <div class="small" style="color:#c0392b">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="small">Confirm</label>
                        <input id="password_confirmation" type="password" class="form-input" name="password_confirmation" required placeholder="••••••••">
                        @if ($errors->has('password_confirmation'))
                            <div class="small" style="color:#c0392b">{{ $errors->first('password_confirmation') }}</div>
                        @endif
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:8px; margin-top:6px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7a6b">
                        <input type="checkbox" name="terms" required> I agree to the Terms of Service and Privacy Policy
                    </label>
                </div>

                <div class="form-actions">
                    <div class="muted small">Already part of the ritual? <a href="{{ route('login') }}" style="color:#365b40; font-weight:600"> Login</a></div>
                    <button type="submit" class="btn-primary">Create Account →</button>
                </div>
            </form>

            <div style="margin-top:24px; padding-top:16px; border-top:1px solid #e6ece3; display:flex; justify-content:space-between; align-items:center;">
                <a href="#" class="small" style="color:#56705a">Support</a>
                <select style="font-size:13px; color:#6b7a6b; background:none; border:none; cursor:pointer;">
                    <option>English (US)</option>
                </select>
            </div>
        </div>
    </div>
</div>
@endsection
