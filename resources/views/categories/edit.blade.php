@extends('layouts.app')

@section('title', 'Edit Category')
@section('heading', 'Edit Category')
@section('subheading', 'Update the name for this expense category.')

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')
            @include('categories.form')
        </form>
    </div>
@endsection
