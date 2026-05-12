<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_status_can_be_updated_with_json_response(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Food', 'status' => true]);

        $response = $this->actingAs($user)
            ->patchJson(route('api.categories.change-status', $category), [
                'status' => false,
            ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Category status updated successfully.')
            ->assertJsonPath('category.status', false);

        $this->assertFalse($category->fresh()->status);
    }

    public function test_category_status_validation_errors_are_returned(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Food', 'status' => true]);

        $response = $this->actingAs($user)
            ->patchJson(route('api.categories.change-status', $category), [
                'status' => 'invalid',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_category_used_by_expenses_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Travel', 'status' => true]);

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 25,
            'description' => 'Train ticket',
            'spent_at' => '2026-05-10 09:00:00',
        ]);

        $response = $this->actingAs($user)
            ->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('error', 'This category is used by expenses and cannot be deleted.');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}
