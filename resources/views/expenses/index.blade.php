@extends('layouts.app')

@section('title', 'Expenses')
@section('heading', 'Expenses')
@section('subheading', 'Review, filter, export, and manage your recorded spending.')

@section('page-actions')
    <a class="btn btn-secondary" href="{{ route('expenses.export', request()->query()) }}">Export Excel</a>
    <a class="btn btn-primary" href="{{ route('expenses.create') }}">Add expense</a>
@endsection

@section('content')
    @php
        $period = $filters['period'] ?? 'month';
        $monthValue = $filters['month'] ?? now()->format('Y-m');
        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth()->toDateString();
        $rangeStart = \Illuminate\Support\Carbon::parse($dateFrom);
        $rangeEnd = \Illuminate\Support\Carbon::parse($dateTo);
        $dateLabel = match ($period) {
            'week' => 'This Week',
            'year' => 'This Year',
            'custom' => $rangeStart->format('d M Y').' - '.$rangeEnd->format('d M Y'),
            default => 'This Month',
        };
    @endphp

    <form id="expenseFilters" method="GET" action="{{ route('expenses.index') }}"></form>
    <input class="drawer-toggle" id="expense-filter-drawer" type="checkbox">

    <div class="expense-list-card">
        <div class="expense-list-header">
            <div class="expense-list-title">
                <h2>Expense list</h2>
                <p class="muted">
                    @if (($filters['all'] ?? false))
                        Showing all months.
                    @else
                        Showing {{ $rangeStart->format('d M Y') }} to {{ $rangeEnd->format('d M Y') }}.
                    @endif
                </p>
            </div>

            <div class="expense-list-meta">
                <div class="filtered-total">
                    <div class="label">Filtered total</div>
                    <div class="value money">₹{{ number_format($total, 2) }}</div>
                </div>

                <div class="top-list-tools">
                    <label class="btn btn-secondary" for="expense-filter-drawer">Filters</label>

                    <details class="date-filter-wrap">
                        <summary class="date-trigger">
                            <strong>{{ $dateLabel }}</strong>
                            <span>{{ ($filters['all'] ?? false) ? 'All dates' : $rangeStart->format('d M').' - '.$rangeEnd->format('d M Y') }}</span>
                        </summary>

                        <div class="date-popover">
                            <div class="date-range-title">{{ $rangeStart->format('d M Y') }} - {{ $rangeEnd->format('d M Y') }}</div>
                            <div class="date-popover-body">
                                <div class="quick-dates">
                                    <input class="quick-date-input" id="period-month" type="radio" name="period" value="month" form="expenseFilters" @checked($period === 'month')>
                                    <label class="quick-date" for="period-month">This Month</label>

                                    <input class="quick-date-input" id="period-week" type="radio" name="period" value="week" form="expenseFilters" @checked($period === 'week')>
                                    <label class="quick-date" for="period-week">This Week</label>

                                    <input class="quick-date-input" id="period-year" type="radio" name="period" value="year" form="expenseFilters" @checked($period === 'year')>
                                    <label class="quick-date" for="period-year">This Year</label>

                                    <a class="quick-date" href="{{ route('expenses.index', ['period' => 'month', 'month' => now()->subMonth()->format('Y-m'), 'per_page' => $filters['per_page'] ?? 10]) }}">Last Month</a>
                                    <a class="quick-date" href="{{ route('expenses.index', ['period' => 'custom', 'date_from' => now()->subDays(89)->toDateString(), 'date_to' => now()->toDateString(), 'per_page' => $filters['per_page'] ?? 10]) }}">Last 90 Days</a>

                                    <input class="quick-date-input" id="period-custom" type="radio" name="period" value="custom" form="expenseFilters" @checked($period === 'custom')>
                                    <label class="quick-date" for="period-custom">Custom Date</label>
                                </div>

                                <div class="date-custom-card">
                                    <div class="date-custom-title">
                                        <span>Custom range</span>
                                        <label class="quick-date" for="period-custom" style="min-height:30px; padding:5px 9px;">Use custom</label>
                                    </div>

                                    <div class="custom-date-grid">
                                        <div class="field" style="margin:0;">
                                            <label for="date_from">From</label>
                                            <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" form="expenseFilters" onchange="document.getElementById('period-custom').checked = true">
                                        </div>

                                        <div class="field" style="margin:0;">
                                            <label for="date_to">To</label>
                                            <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" form="expenseFilters" onchange="document.getElementById('period-custom').checked = true">
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="month" value="{{ $monthValue }}" form="expenseFilters">
                            </div>

                            <div class="date-popover-footer">
                                <a class="btn btn-secondary" href="{{ route('expenses.index', ['all' => 1, 'per_page' => $filters['per_page'] ?? 10]) }}">Clear</a>
                                <button class="btn btn-primary" type="submit" form="expenseFilters">Apply</button>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </div>

        <div class="panel list-panel">
            <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenses as $expense)
                    <tr>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->category->name }}</td>
                        <td>{{ $expense->spent_at->format('d M Y, h:i A') }}</td>
                        <td>₹{{ number_format($expense->amount, 2) }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary" href="{{ route('expenses.edit', $expense) }}">Edit</a>
                                <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No expenses found.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        <div class="list-footer">
            <div class="muted">
                Showing
                @if ($expenses->total() > 0)
                    {{ $expenses->firstItem() }}-{{ $expenses->lastItem() }} of
                @endif
                {{ $expenses->total() }} expense {{ $expenses->total() === 1 ? 'entry' : 'entries' }}.
            </div>

            <div class="list-controls">
                <div class="list-control" style="min-width:118px;">
                    <label for="per_page">Rows per page</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()" form="expenseFilters">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected((int) ($filters['per_page'] ?? 10) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <nav class="compact-pagination" aria-label="Pagination">
                @if ($expenses->onFirstPage())
                    <span class="page-chip disabled">Prev</span>
                @else
                    <a class="page-chip" href="{{ $expenses->previousPageUrl() }}">Prev</a>
                @endif

                @php
                    $startPage = max(1, $expenses->currentPage() - 2);
                    $endPage = min($expenses->lastPage(), $expenses->currentPage() + 2);
                @endphp

                @if ($startPage > 1)
                    <a class="page-chip" href="{{ $expenses->url(1) }}">1</a>
                    @if ($startPage > 2)
                        <span class="page-chip disabled">...</span>
                    @endif
                @endif

                @foreach (range($startPage, $endPage) as $page)
                    @if ($page == $expenses->currentPage())
                        <span class="page-chip active">{{ $page }}</span>
                    @else
                        <a class="page-chip" href="{{ $expenses->url($page) }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($endPage < $expenses->lastPage())
                    @if ($endPage < $expenses->lastPage() - 1)
                        <span class="page-chip disabled">...</span>
                    @endif
                    <a class="page-chip" href="{{ $expenses->url($expenses->lastPage()) }}">{{ $expenses->lastPage() }}</a>
                @endif

                @if ($expenses->hasMorePages())
                    <a class="page-chip" href="{{ $expenses->nextPageUrl() }}">Next</a>
                @else
                    <span class="page-chip disabled">Next</span>
                @endif
                </nav>
            </div>
        </div>
    </div>

    @if (($filters['all'] ?? false))
        <input type="hidden" name="all" value="1" form="expenseFilters">
    @endif

    <label class="drawer-overlay" for="expense-filter-drawer"></label>

    <aside class="filter-drawer" aria-label="Expense filters">
        <div class="drawer-header">
            <h2>Filters</h2>
            <label class="drawer-close" for="expense-filter-drawer" aria-label="Close filters">&times;</label>
        </div>

        <div class="drawer-body">
            <div class="field" style="margin:0;">
                <label for="search">Search description</label>
                <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Lunch, taxi, groceries" form="expenseFilters">
            </div>

            <div class="field" style="margin:0;">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" form="expenseFilters">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="drawer-footer">
            <a class="btn btn-secondary" href="{{ route('expenses.index', ['all' => 1, 'per_page' => $filters['per_page'] ?? 10]) }}">Clear Filter</a>
            <button class="btn btn-primary" type="submit" form="expenseFilters">Apply Filter</button>
        </div>
    </aside>
@endsection
