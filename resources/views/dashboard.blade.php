@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'A quick view of spending pace, category movement, and recent activity.')

@section('content')
    <div class="grid grid-3">
        <div class="soft-panel stat-card">
            <div class="stat-label">Spent this month</div>
            <div class="stat money">₹{{ number_format($monthlyTotal, 2) }}</div>
            @if (! is_null($monthChange))
                <span class="badge {{ $monthChange > 0 ? 'badge-up' : 'badge-down' }}">
                    {{ $monthChange > 0 ? '+' : '' }}{{ number_format($monthChange, 1) }}% vs last month
                </span>
            @else
                <span class="badge badge-neutral">No previous month data</span>
            @endif
        </div>
        <div class="soft-panel stat-card">
            <div class="stat-label">Projected month end</div>
            <div class="stat money">₹{{ number_format($projectedTotal, 2) }}</div>
            <span class="muted">₹{{ number_format($dailyAverage, 2) }} average per day</span>
        </div>
        <div class="soft-panel stat-card">
            <div class="stat-label">Expense entries</div>
            <div class="stat">{{ $expenseCount }}</div>
            <span class="muted">{{ $categoryCount }} active categories</span>
        </div>
    </div>

    <div class="grid grid-2" style="margin-top:18px;">
        <div class="panel">
            <h2 style="margin-top:0;">Smart insights</h2>
            <ul class="insight-list">
                <li class="insight-item">
                    <strong>Top category</strong>
                    <span class="muted">
                        @if ($topCategory)
                            {{ $topCategory->name }} leads this month at ₹{{ number_format($topCategory->total, 2) }}.
                        @else
                            Add a few expenses to see your spending pattern.
                        @endif
                    </span>
                </li>
                <li class="insight-item">
                    <strong>Largest transaction</strong>
                    <span class="muted">
                        @if ($largestExpense)
                            {{ $largestExpense->description }} was ₹{{ number_format($largestExpense->amount, 2) }} in {{ $largestExpense->category->name }}.
                        @else
                            No transaction found for this month.
                        @endif
                    </span>
                </li>
                <li class="insight-item">
                    <strong>Current pace</strong>
                    <span class="muted">At this pace, month-end spending is projected near ₹{{ number_format($projectedTotal, 2) }}.</span>
                </li>
            </ul>
        </div>

        <div class="panel">
            <h2 style="margin-top:0;">This month by category</h2>
            <div class="chart-list">
                @forelse ($categoryBreakdown as $row)
                    @php
                        $width = $monthlyTotal > 0 ? max(4, ($row->total / $monthlyTotal) * 100) : 0;
                    @endphp
                    <div class="chart-row">
                        <div class="chart-meta">
                            <span>{{ $row->name }}</span>
                            <strong>₹{{ number_format($row->total, 2) }}</strong>
                        </div>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $width }}%;"></div></div>
                    </div>
                @empty
                    <p class="muted">No expenses yet for this month.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="panel" style="margin-top:18px;">
        <h2 style="margin-top:0;">Recent expenses</h2>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentExpenses as $expense)
                    <tr>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->category->name }}</td>
                        <td>{{ $expense->spent_at->format('d M Y') }}</td>
                        <td>₹{{ number_format($expense->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No expenses yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
