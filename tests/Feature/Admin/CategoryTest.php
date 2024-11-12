<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Category;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（カテゴリ一覧ページ）
    // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_user_cannot_access_admin_categories_index()
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_general_user_cannot_access_admin_categories_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_user_can_access_admin_categories_index()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.categories.index'));
        $response->assertStatus(200);
    }

    // storeアクション（カテゴリ登録機能）
    // 未ログインのユーザーはカテゴリを登録できない
    public function test_guest_user_cannot_access_categories_store()
    {
        $category = Category::factory()->create();

        $response = $this->post(route('admin.categories.store', $category));
        $this->assertDatabaseMissing('categories', $category->toArray());
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_general_user_cannot_access_categories_store()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.categories.store', $category));
        $this->assertDatabaseMissing('categories', $category->toArray());
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを登録できる
    public function test_admin_user_can_access_admin_categories_store()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $categoryData = Category::factory()->make()->toArray();

        $response = $this->actingAs($adminUser, 'admin')->post(route('admin.categories.store', $categoryData));
        $this->assertDatabaseHas('categories', $categoryData);
        $response->assertRedirect(route('admin.categories.index'));
    }

    // updateアクション（カテゴリ更新機能）
    // 未ログインのユーザーはカテゴリを更新できない
    public function test_guest_user_cannot_update_category()
    {
        $category = Category::factory()->create();

        $update_category = [
            'name' => 'テスト2',
        ];

        $response = $this->patch(route('admin.categories.update', $category), $update_category);
        $this->assertDatabaseMissing('categories', $update_category);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_general_user_cannot_update_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $update_category = [
            'name' => 'テスト2',
        ];

        $response = $this->actingAs($user)->patch(route('admin.categories.update', $category), $update_category);
        $this->assertDatabaseMissing('categories', $update_category);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを更新できる
    public function test_admin_user_can_update_category()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $category = Category::factory()->create();

        $update_category = [
            'name' => 'テスト2',
        ];

        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.categories.update', $category), $update_category);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'テスト2',
        ] );
        $response->assertRedirect(route('admin.categories.index'));
    }

    // destroyアクション（カテゴリ削除機能）
    // 未ログインのユーザーはカテゴリを削除できない
    public function test_guest_user_cannot_destroy_category()
    {
        $category = Category::factory()->create();
        $categoryData = $category->toArray();

        unset($categoryData['created_at'], $categoryData['updated_at']);

        $response = $this->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseHas('categories', $categoryData);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_general_user_cannot_destroy_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $categoryData = $category->toArray();

        unset($categoryData['created_at'], $categoryData['updated_at']);

        $response = $this->actingAs($user, 'web')->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseHas('categories', $categoryData);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを削除できる
    public function test_admin_user_can_destroy_category()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $category = Category::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseMissing('categories', $category->toArray());
        $response->assertRedirect(route('admin.categories.index'));
    }
}
