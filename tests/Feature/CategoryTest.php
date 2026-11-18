<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_default_and_own_categories_only(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $default = Category::create(['name' => 'Makanan', 'type' => 'expense']);
        $own = Category::create(['name' => 'Gaji', 'type' => 'income', 'user_id' => $user->id]);
        $foreign = Category::create(['name' => 'Pribadi', 'type' => 'expense', 'user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson(route('categories.index'));

        $response->assertOk()
            ->assertJsonFragment(['id' => $own->id, 'name' => 'Gaji'])
            ->assertJsonFragment(['id' => $default->id, 'name' => 'Makanan'])
            ->assertJsonMissing(['id' => $foreign->id]);

        $this->assertDatabaseHas('categories', ['id' => $default->id]);
    }

    public function test_user_cannot_update_or_delete_another_users_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::create(['name' => 'Pribadi', 'type' => 'expense', 'user_id' => $otherUser->id]);

        $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Diubah',
            'type' => 'expense',
        ])->assertForbidden();

        $this->actingAs($user)->delete(route('categories.destroy', $category))->assertForbidden();
    }

    public function test_user_cannot_modify_default_category_but_admin_can(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Makanan', 'type' => 'expense']);

        $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Diubah',
            'type' => 'expense',
        ])->assertForbidden();

        $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => 'Diubah Admin',
            'type' => 'expense',
        ])->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Diubah Admin']);
    }
}
