@extends('layouts.app')

@section('title', 'Edit Expense')
@section('heading', 'Edit Expense')
@section('subheading', 'Update the details for this expense entry.')

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('expenses.update', $expense) }}">
            @csrf
            @method('PUT')
            @include('expenses.form')
        </form>
    </div>
@endsection
