@extends('layouts.app')

@section('title', 'Reports')
@section('heading', 'Reports')
@section('subheading', 'Analyze monthly totals, category distribution, and daily spending movement.')

@section('content')
    <div class="panel" style="margin-bottom:18px;">
        <form method="GET" action="{{ route('reports.index') }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
            <div class="field" style="margin:0;">
                <label for="month">Report month</label>
                <input id="month" type="month" name="month" value="{{ $month }}">
            </div>
            <button class="btn btn-primary" type="submit">Run report</button>
        </form>
    </div>

    <div class="grid grid-3">
        <div class="panel">
            <div class="muted">Total for {{ $month }}</div>
            <div class="stat money">₹{{ number_format($monthTotal, 2) }}</div>
        </div>
        <div class="panel">
            <div class="muted">Average daily expense</div>
            <div class="stat money">₹{{ number_format($averageDaily, 2) }}</div>
        </div>
        <div class="panel">
            <div class="muted">Categories used</div>
            <div class="stat">{{ $totalsByCategory->count() }}</div>
        </div>
    </div>

    <div class="grid grid-2" style="margin-top:18px;">
        <div class="panel">
            <h2 style="margin-top:0;">Monthly category chart</h2>
            <div class="chart-list" style="margin-bottom:18px;">
                @forelse ($totalsByCategory as $row)
                    @php
                        $width = $monthTotal > 0 ? max(4, ($row->total / $monthTotal) * 100) : 0;
                    @endphp
                    <div class="chart-row">
                        <div class="chart-meta">
                            <span>{{ $row->name }}</span>
                            <strong>₹{{ number_format($row->total, 2) }}</strong>
                        </div>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $width }}%;"></div></div>
                    </div>
                @empty
                    <p class="muted">No expenses in this month.</p>
                @endforelse
            </div>

            <h2>Monthly total per category</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($totalsByCategory as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>₹{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="muted">No expenses in this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h2 style="margin-top:0;">Daily spending chart</h2>
            <div class="chart-list" style="margin-bottom:18px;">
                @forelse ($dailyTotals as $row)
                    @php
                        $width = max(4, ($row->total / $largestDailyTotal) * 100);
                    @endphp
                    <div class="chart-row">
                        <div class="chart-meta">
                            <span>{{ \Illuminate\Support\Carbon::parse($row->day)->format('d M') }}</span>
                            <strong>₹{{ number_format($row->total, 2) }}</strong>
                        </div>
                        <div class="bar-track"><div class="bar-fill alt" style="width: {{ $width }}%;"></div></div>
                    </div>
                @empty
                    <p class="muted">No daily spending found for this month.</p>
                @endforelse
            </div>

            <h2>User total per category</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($userCategoryTotals as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>₹{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="muted">No expenses recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
