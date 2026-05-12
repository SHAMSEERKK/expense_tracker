<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_expense(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Food', 'status' => true]);

        $response = $this->actingAs($user)->post('/expenses', [
            'amount' => '42.50',
            'description' => 'Lunch with team',
            'category_id' => $category->id,
            'spent_at' => '2026-05-11 12:30:00',
        ]);

        $response->assertRedirect('/expenses');
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'description' => 'Lunch with team',
        ]);
    }

    public function test_user_cannot_edit_another_users_expense(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::create(['name' => 'Travel', 'status' => true]);
        $expense = Expense::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'amount' => 20,
            'description' => 'Train ticket',
            'spent_at' => '2026-05-10 09:00:00',
        ]);

        $response = $this->actingAs($otherUser)->get("/expenses/{$expense->id}/edit");

        $response->assertForbidden();
    }

    public function test_expenses_can_be_filtered_and_exported(): void
    {
        $user = User::factory()->create();
        $food = Category::create(['name' => 'Food', 'status' => true]);
        $travel = Category::create(['name' => 'Travel', 'status' => true]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $food->id,
            'amount' => 12.75,
            'description' => 'Office lunch',
            'spent_at' => '2026-05-11 13:00:00',
        ]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $travel->id,
            'amount' => 90,
            'description' => 'Airport ride',
            'spent_at' => '2026-04-11 13:00:00',
        ]);

        $response = $this->actingAs($user)->get('/expenses?category_id='.$food->id.'&month=2026-05&search=lunch');

        $response->assertOk();
        $response->assertSee('Office lunch');
        $response->assertDontSee('Airport ride');

        $export = $this->actingAs($user)->get('/expenses/export?category_id='.$food->id.'&month=2026-05&search=lunch');

        $export->assertOk();
        $export->assertHeader('content-type', 'application/vnd.ms-excel');

        $spreadsheet = $export->streamedContent();

        $this->assertStringContainsString('Office lunch', $spreadsheet);
        $this->assertStringNotContainsString('Airport ride', $spreadsheet);
    }

    public function test_expense_index_defaults_to_current_month_and_paginates_filtered_results(): void
    {
        $this->travelTo('2026-05-12 10:00:00');

        $user = User::factory()->create();
        $food = Category::create(['name' => 'Food', 'status' => true]);
        $travel = Category::create(['name' => 'Travel', 'status' => true]);

        for ($day = 1; $day <= 7; $day++) {
            Expense::create([
                'user_id' => $user->id,
                'category_id' => $food->id,
                'amount' => 10 + $day,
                'description' => "May food {$day}",
                'spent_at' => "2026-05-{$day} 12:00:00",
            ]);
        }

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $food->id,
            'amount' => 40,
            'description' => 'April food',
            'spent_at' => '2026-04-10 12:00:00',
        ]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $travel->id,
            'amount' => 80,
            'description' => 'April travel',
            'spent_at' => '2026-04-11 12:00:00',
        ]);

        $defaultResponse = $this->actingAs($user)->get('/expenses?per_page=5');

        $defaultResponse->assertOk();
        $defaultResponse->assertSee('Showing 01 May 2026 to 31 May 2026');
        $defaultResponse->assertSee('May food 7');
        $defaultResponse->assertDontSee('May food 2');
        $defaultResponse->assertDontSee('April food');

        $filteredResponse = $this->actingAs($user)
            ->get('/expenses?category_id='.$food->id.'&month=2026-04&per_page=5');

        $filteredResponse->assertOk();
        $filteredResponse->assertSee('Showing 01 Apr 2026 to 30 Apr 2026');
        $filteredResponse->assertSee('April food');
        $filteredResponse->assertDontSee('April travel');
        $filteredResponse->assertDontSee('May food 7');
    }

    public function test_expense_index_uses_eager_loading_with_stable_query_count(): void
    {
        $this->travelTo('2026-05-12 10:00:00');

        $user = User::factory()->create();
        $food = Category::create(['name' => 'Food', 'status' => true]);
        $travel = Category::create(['name' => 'Travel', 'status' => true]);

        for ($index = 1; $index <= 30; $index++) {
            Expense::create([
                'user_id' => $user->id,
                'category_id' => $index % 2 === 0 ? $food->id : $travel->id,
                'amount' => 10,
                'description' => "Expense {$index}",
                'spent_at' => '2026-05-10 12:00:00',
            ]);
        }

        DB::enableQueryLog();
        DB::flushQueryLog();

        $response = $this->actingAs($user)->get('/expenses?per_page=25');
        $queryCount = count(DB::getQueryLog());

        DB::disableQueryLog();

        $response->assertOk();
        $this->assertLessThanOrEqual(8, $queryCount);
    }
}
