<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_smart_insights(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Food', 'status' => true]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 125,
            'description' => 'Weekly groceries',
            'spent_at' => now()->startOfMonth()->addDays(2),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Smart insights');
        $response->assertSee('Top category');
        $response->assertSee('Projected month end');
        $response->assertSee('Weekly groceries');
    }
}
