@extends('layouts.app')

@section('title', 'Add Category')
@section('heading', 'Add Category')
@section('subheading', 'Create a reusable category for expense entry.')

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            @include('categories.form', ['category' => null])
        </form>
    </div>
@endsection
