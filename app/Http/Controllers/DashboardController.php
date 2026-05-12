<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $previousMonthStart = $startOfMonth->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $previousMonthStart->copy()->endOfMonth();

        $monthlyTotal = Expense::where('user_id', Auth::id())
            ->whereBetween('spent_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $previousMonthTotal = Expense::where('user_id', Auth::id())
            ->whereBetween('spent_at', [$previousMonthStart, $previousMonthEnd])
            ->sum('amount');

        $expenseCount = Expense::where('user_id', Auth::id())->count();
        $categoryCount = Category::where('status', true)->count();
        $dailyAverage = $monthlyTotal / max(1, Carbon::now()->day);
        $projectedTotal = $dailyAverage * $startOfMonth->daysInMonth;
        $monthChange = $previousMonthTotal > 0
            ? (($monthlyTotal - $previousMonthTotal) / $previousMonthTotal) * 100
            : null;

        $categoryBreakdown = Expense::query()
            ->select('categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'categories.id', '=', 'expenses.category_id')
            ->where('expenses.user_id', Auth::id())
            ->whereBetween('expenses.spent_at', [$startOfMonth, $endOfMonth])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topCategory = $categoryBreakdown->first();
        $largestExpense = Expense::with('category')
            ->where('user_id', Auth::id())
            ->whereBetween('spent_at', [$startOfMonth, $endOfMonth])
            ->orderByDesc('amount')
            ->first();
        $recentExpenses = Expense::with('category')
            ->where('user_id', Auth::id())
            ->latest('spent_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'monthlyTotal',
            'previousMonthTotal',
            'expenseCount',
            'categoryCount',
            'dailyAverage',
            'projectedTotal',
            'monthChange',
            'categoryBreakdown',
            'topCategory',
            'largestExpense',
            'recentExpenses'
        ));
    }
}
