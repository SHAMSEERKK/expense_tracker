<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseFilterRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseFilterService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    public function __construct(private readonly ExpenseFilterService $expenseFilterService)
    {
    }

    public function index(ExpenseFilterRequest $request): View
    {
        $filters = $this->expenseFilterService->normalize($request->validated(), $request->boolean('all'));
        $categories = $this->expenseFilterService->activeCategories();
        $total = (clone $this->expenseFilterService->query($filters, Auth::id()))->sum('amount');
        $expenses = $this->expenseFilterService->query($filters, Auth::id())
            ->latest('spent_at')
            ->paginate($filters['per_page'])
            ->withQueryString();
        $perPageOptions = $this->expenseFilterService->perPageOptions();

        return view('expenses.index', compact('categories', 'expenses', 'filters', 'total', 'perPageOptions'));
    }

    public function create(): View
    {
        return view('expenses.create', [
            'categories' => $this->expenseFilterService->activeCategories(),
        ]);
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        Expense::create($request->validated() + [
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    public function show(Expense $expense): RedirectResponse
    {
        $this->authorizeOwner($expense);

        return redirect()->route('expenses.edit', $expense);
    }

    public function edit(Expense $expense): View
    {
        $this->authorizeOwner($expense);

        return view('expenses.edit', [
            'expense' => $expense,
            'categories' => $this->expenseFilterService->activeCategories(),
        ]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorizeOwner($expense);

        $expense->update($request->validated());

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->authorizeOwner($expense);

        try {
            $expense->delete();
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('expenses.index')
                ->with('error', 'Expense could not be deleted. Please try again.');
        }

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function export(ExpenseFilterRequest $request): StreamedResponse
    {
        $filters = $this->expenseFilterService->normalize($request->validated(), $request->boolean('all'));
        $fileName = 'expenses-'.now()->format('Y-m-d-His').'.xls';

        return response()->streamDownload(function () use ($filters) {
            echo '<table border="1">';
            echo '<thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th></tr></thead><tbody>';

            $this->expenseFilterService->query($filters, Auth::id())
                ->oldest('spent_at')
                ->chunk(200, function ($expenses) {
                    foreach ($expenses as $expense) {
                        echo '<tr>';
                        echo '<td>'.e($expense->spent_at->format('Y-m-d H:i:s')).'</td>';
                        echo '<td>'.e($expense->category->name).'</td>';
                        echo '<td>'.e($expense->description).'</td>';
                        echo '<td>'.e($expense->amount).'</td>';
                        echo '</tr>';
                    }
                });

            echo '</tbody></table>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    private function authorizeOwner(Expense $expense): void
    {
        abort_unless($expense->user_id === Auth::id(), 403);
    }
}
