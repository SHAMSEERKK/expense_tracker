@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="panel auth-card">
        <h1 style="margin-top:0;">Create account</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="field">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <button class="btn btn-primary" type="submit" style="width:100%;">Create account</button>
        </form>

        <p class="muted" style="margin-bottom:0;">Already registered? <a href="{{ route('login') }}" style="color:var(--brand);">Log in</a></p>
    </div>
@endsection
