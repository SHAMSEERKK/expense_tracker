<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ExpenseFilterService
{
    /**
     * @param array<string, mixed> $filters
     * @return array{
     *     all: bool,
     *     per_page: int,
     *     period?: string,
     *     category_id?: int|null,
     *     date_from?: string,
     *     date_to?: string,
     *     month?: string,
     *     search?: string|null
     * }
     */
    public function normalize(array $filters, bool $viewAll = false): array
    {
        $filters['period'] = $filters['period'] ?? 'month';
        $filters['all'] = $viewAll
            && blank($filters['month'] ?? null)
            && blank($filters['date_from'] ?? null)
            && blank($filters['date_to'] ?? null);
        $filters['per_page'] = (int) ($filters['per_page'] ?? 10);

        if (! $filters['all'] && blank($filters['month'] ?? null)) {
            $filters['month'] = now()->format('Y-m');
        }

        if (! $filters['all']) {
            [$filters['date_from'], $filters['date_to']] = $this->dateRange($filters);
        }

        return $filters;
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function query(array $filters, int $userId): Builder
    {
        return Expense::query()
            ->with('category')
            ->forUser($userId)
            ->forCategory($filters['category_id'] ?? null)
            ->when($filters['date_from'] ?? null, function (Builder $query) use ($filters): void {
                $query->withinDateRange(
                    Carbon::parse($filters['date_from']),
                    Carbon::parse($filters['date_to'])
                );
            })
            ->matchingDescription($filters['search'] ?? null);
    }

    /**
     * @return Collection<int, Category>
     */
    public function activeCategories(): Collection
    {
        return Category::query()
            ->active()
            ->orderedByName()
            ->get();
    }

    /**
     * @return array<int, int>
     */
    public function perPageOptions(): array
    {
        return [5, 10, 25, 50, 100];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{0: string, 1: string}
     */
    private function dateRange(array $filters): array
    {
        if (($filters['period'] ?? 'month') === 'custom') {
            $start = filled($filters['date_from'] ?? null)
                ? Carbon::parse($filters['date_from'])->startOfDay()
                : now()->startOfMonth();
            $end = filled($filters['date_to'] ?? null)
                ? Carbon::parse($filters['date_to'])->endOfDay()
                : $start->copy()->endOfDay();

            return [$start->toDateString(), $end->toDateString()];
        }

        $anchor = filled($filters['month'] ?? null)
            ? Carbon::createFromFormat('!Y-m', $filters['month'])
            : now();

        return match ($filters['period'] ?? 'month') {
            'week' => [$anchor->copy()->startOfWeek()->toDateString(), $anchor->copy()->endOfWeek()->toDateString()],
            'year' => [$anchor->copy()->startOfYear()->toDateString(), $anchor->copy()->endOfYear()->toDateString()],
            default => [$anchor->copy()->startOfMonth()->toDateString(), $anchor->copy()->endOfMonth()->toDateString()],
        };
    }
}
