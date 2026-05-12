<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_shows_monthly_category_totals(): void
    {
        $user = User::factory()->create();
        $food = Category::create(['name' => 'Food', 'status' => true]);
        $travel = Category::create(['name' => 'Transportation', 'status' => true]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $food->id,
            'amount' => 100,
            'description' => 'Groceries',
            'spent_at' => '2026-05-05 10:00:00',
        ]);
        Expense::create([
            'user_id' => $user->id,
            'category_id' => $travel->id,
            'amount' => 50,
            'description' => 'Bus pass',
            'spent_at' => '2026-05-06 10:00:00',
        ]);

        $response = $this->actingAs($user)->get('/reports?month=2026-05');

        $response->assertOk();
        $response->assertSee('Food');
        $response->assertSee('₹100.00');
        $response->assertSee('Transportation');
        $response->assertSee('₹50.00');
        $response->assertSee('Monthly category chart');
        $response->assertSee('Daily spending chart');
    }

    public function test_report_uses_stable_query_count_for_aggregates(): void
    {
        $user = User::factory()->create();
        $food = Category::create(['name' => 'Food', 'status' => true]);
        $travel = Category::create(['name' => 'Transportation', 'status' => true]);

        for ($index = 1; $index <= 40; $index++) {
            Expense::create([
                'user_id' => $user->id,
                'category_id' => $index % 2 === 0 ? $food->id : $travel->id,
                'amount' => 20,
                'description' => "Report expense {$index}",
                'spent_at' => '2026-05-06 10:00:00',
            ]);
        }

        DB::enableQueryLog();
        DB::flushQueryLog();

        $response = $this->actingAs($user)->get('/reports?month=2026-05');
        $queryCount = count(DB::getQueryLog());

        DB::disableQueryLog();

        $response->assertOk();
        $this->assertLessThanOrEqual(6, $queryCount);
    }
}
