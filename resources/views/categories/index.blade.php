@extends('layouts.app')

@section('title', 'Categories')
@section('heading', 'Categories')
@section('subheading', 'Manage the predefined categories used when recording expenses.')

@section('page-actions')
    <a class="btn btn-primary" href="{{ route('categories.create') }}">Add category</a>
@endsection

@section('content')
    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>
                            <form method="POST" action="{{ route('categories.change-status', $category) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $category->status ? 0 : 1 }}">
                                <button class="toggle {{ $category->status ? 'active' : 'inactive' }}" type="submit">
                                    <span class="toggle-dot"></span>
                                    <span>{{ $category->status ? 'Active' : 'Inactive' }}</span>
                                </button>
                            </form>
                        </td>
                        <td>{{ $category->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary" href="{{ route('categories.edit', $category) }}">Edit</a>
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $categories->links() }}</div>
@endsection
