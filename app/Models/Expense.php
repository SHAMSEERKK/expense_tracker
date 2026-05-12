<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'description',
        'spent_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId, function (Builder $query, int $categoryId): void {
            $query->where('category_id', $categoryId);
        });
    }

    public function scopeWithinDateRange(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('spent_at', [
            $start->copy()->startOfDay(),
            $end->copy()->endOfDay(),
        ]);
    }

    public function scopeMatchingDescription(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $query, string $search): void {
            $query->where('description', 'like', "%{$search}%");
        });
    }
}
