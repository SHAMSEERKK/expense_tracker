@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="panel auth-card">
        <h1 style="margin-top:0;">Log in</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field" style="display:flex; gap:8px; align-items:center;">
                <input id="remember" type="checkbox" name="remember" value="1" style="width:auto;">
                <label for="remember" style="margin:0;">Remember me</label>
            </div>

            <button class="btn btn-primary" type="submit" style="width:100%;">Log in</button>
        </form>

        <p class="muted" style="margin-bottom:0;">New here? <a href="{{ route('register') }}" style="color:var(--brand); font-weight:700;">Create an account</a></p>
    </div>
@endsection
