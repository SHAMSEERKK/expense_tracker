@extends('layouts.app')

@section('title', 'Profile')
@section('heading', 'Profile Details')
@section('subheading', 'Keep your account name and email address up to date.')

@section('content')
    <div class="panel" style="max-width:640px;">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button class="btn btn-primary" type="submit">Update profile</button>
        </form>
    </div>
@endsection
