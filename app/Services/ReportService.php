<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ReportService
{
    /**
     * @return array<string, mixed>
     */
    public function monthlyReport(int $userId, ?string $month = null): array
    {
        $month = $month ?: now()->format('Y-m');
        $start = Carbon::createFromFormat('!Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $totalsByCategory = $this->categoryTotals($userId, $start, $end);

        $monthTotal = (float) $totalsByCategory->sum('total');
        $dailyTotals = Expense::query()
            ->forUser($userId)
            ->withinDateRange($start, $end)
            ->selectRaw('DATE(spent_at) as day, SUM(amount) as total')
            ->groupByRaw('DATE(spent_at)')
            ->orderBy('day')
            ->get();

        return [
            'month' => $month,
            'monthTotal' => $monthTotal,
            'averageDaily' => $monthTotal / max(1, $start->daysInMonth),
            'totalsByCategory' => $totalsByCategory,
            'dailyTotals' => $dailyTotals,
            'largestDailyTotal' => max(1, (float) $dailyTotals->max('total')),
            'userCategoryTotals' => $this->userCategoryTotals($userId),
        ];
    }

    /**
     * @return Collection<int, Category>
     */
    private function userCategoryTotals(int $userId): Collection
    {
        return Category::query()
            ->whereHas('expenses', function (Builder $query) use ($userId): void {
                $query->forUser($userId);
            })
            ->withSum([
                'expenses as total' => function (Builder $query) use ($userId): void {
                    $query->forUser($userId);
                },
            ], 'amount')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * @return Collection<int, Category>
     */
    private function categoryTotals(int $userId, Carbon $start, Carbon $end): Collection
    {
        return Category::query()
            ->whereHas('expenses', function (Builder $query) use ($userId, $start, $end): void {
                $query->forUser($userId)
                    ->withinDateRange($start, $end);
            })
            ->withSum([
                'expenses as total' => function (Builder $query) use ($userId, $start, $end): void {
                    $query->forUser($userId)
                        ->withinDateRange($start, $end);
                },
            ], 'amount')
            ->orderByDesc('total')
            ->get();
    }
}
