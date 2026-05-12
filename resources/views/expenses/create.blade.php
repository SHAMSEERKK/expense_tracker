@extends('layouts.app')

@section('title', 'Add Expense')
@section('heading', 'Add Expense')
@section('subheading', 'Record a new expense with amount, category, date, and description.')

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            @include('expenses.form', ['expense' => null])
        </form>
    </div>
@endsection
