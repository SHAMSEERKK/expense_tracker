<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeCategoryStatusRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = $this->categoryService->paginated();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): RedirectResponse
    {
        return redirect()->route('categories.edit', $category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function changeStatus(ChangeCategoryStatusRequest $request, Category $category): RedirectResponse|JsonResponse
    {
        $this->categoryService->changeStatus($category, $request->boolean('status'));

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Category status updated successfully.',
                'category' => $category->fresh(),
            ]);
        }

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($this->categoryService->isUsedByExpenses($category)) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'This category is used by expenses and cannot be deleted.');
        }

        try {
            $this->categoryService->delete($category);
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('categories.index')
                ->with('error', 'Category could not be deleted. Please try again.');
        }

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
