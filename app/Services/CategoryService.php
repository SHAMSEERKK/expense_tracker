<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;

class CategoryService
{
    /**
     * @return LengthAwarePaginator<int, Category>
     */
    public function paginated(int $perPage = 10): LengthAwarePaginator
    {
        return Category::latest()->paginate($perPage);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function changeStatus(Category $category, bool $status): bool
    {
        return $category->update([
            'status' => $status,
        ]);
    }

    public function isUsedByExpenses(Category $category): bool
    {
        return $category->expenses()->exists();
    }

    /**
     * @throws QueryException
     */
    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
