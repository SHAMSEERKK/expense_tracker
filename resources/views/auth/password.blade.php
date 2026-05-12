@extends('layouts.app')

@section('title', 'Reset Password')
@section('heading', 'Reset Password')
@section('subheading', 'Change your password using your current password for confirmation.')

@section('content')
    <div class="panel" style="max-width:640px;">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="current_password">Current password</label>
                <input id="current_password" type="password" name="current_password" required>
                @error('current_password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password">New password</label>
                <input id="password" type="password" name="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <button class="btn btn-primary" type="submit">Update password</button>
        </form>
    </div>
@endsection
