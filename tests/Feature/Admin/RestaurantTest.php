<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション
    // 未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_guest_user_cannot_access_admin_restaurants_index()
    {
        $response = $this->get(route('admin.restaurants.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_general_user_cannot_access_admin_restaurants_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_admin_user_can_access_admin_restaurants_index()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.index'));
        $response->assertStatus(200);
    }

    // showアクション
    // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_user_cannot_access_admin_restaurant_show()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('admin.restaurants.show', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_general_user_cannot_access_admin_restaurant_show()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.show', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
    public function test_admin_user_can_access_admin_restaurant_show()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.show', $restaurant));
        $response->assertStatus(200);
    }

    // createアクション
    // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_user_cannot_access_admin_restaurant_create()
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.restaurants.create', $user));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_general_user_cannot_access_admin_restaurant_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.create', $user));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_admin_user_can_access_admin_restaurant_create()
    {
        $user = User::factory()->create();
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.create', $user));
        $response->assertStatus(200);
    }

    // storeアクション
    // 未ログインのユーザーは店舗を登録できない
    public function test_guest_user_cannot_access_restaurant_store()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('admin.restaurants.store', $restaurant));
        $this->assertDatabaseMissing('restaurants', $restaurant->toArray());
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を登録できない
    public function test_general_user_cannot_access_restaurant_store()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.restaurants.store', $restaurant));
        $this->assertDatabaseMissing('restaurants', $restaurant->toArray());
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は店舗を登録できる
    public function test_admin_user_can_access_admin_restaurant_store()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $restaurantData = Restaurant::factory()->make()->toArray();

        $response = $this->actingAs($adminUser, 'admin')->post(route('admin.restaurants.store', $restaurantData));
        $this->assertDatabaseHas('restaurants', $restaurantData);
        $response->assertRedirect(route('admin.restaurants.index'));
    }

    // editアクション？
    // 未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_guest_user_cannot_access_admin_restaurant_edit()
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.restaurants.edit', $user));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_general_user_cannot_access_admin_restaurant_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.edit', $user));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_admin_user_can_access_admin_restaurant_edit()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.edit', $restaurant));
        $response->assertStatus(200);
    }

    // updateアクション？
    // 未ログインのユーザーは店舗を更新できない
    public function test_guest_user_cannot_update_restaurant()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $update_restaurant = [
            'name' => 'テスト2',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ];

        $response = $this->patch(route('admin.restaurants.update', $restaurant), $update_restaurant);
        $this->assertDatabaseMissing('restaurants', $update_restaurant);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を更新できない
    public function test_general_user_cannot_update_restaurant()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $update_restaurant = [
            'name' => 'テスト2',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ];

        $response = $this->actingAs($user)->patch(route('admin.restaurants.update', $restaurant), $update_restaurant);
        $this->assertDatabaseMissing('restaurants', $update_restaurant);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は店舗を更新できる
    public function test_admin_user_can_update_restaurant()
    {
        // $user = User::factory()->create();
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $restaurant = Restaurant::factory()->create();

        $update_restaurant = [
            'name' => 'テスト2',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ];

        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.restaurants.update', $restaurant), $update_restaurant);
        $this->assertDatabaseHas('restaurants', $update_restaurant);
        $response->assertRedirect(route('admin.restaurants.show', $restaurant));
    }

    // destroyアクション
    // 未ログインのユーザーは店舗を削除できない
    public function test_guest_user_cannot_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $restaurantData = $restaurant->toArray();

        $restaurantData['lowest_price'] = (int) $restaurantData['lowest_price'];
        $restaurantData['highest_price'] = (int) $restaurantData['highest_price'];
        $restaurantData['seating_capacity'] = (int) $restaurantData['seating_capacity'];

        unset($restaurantData['created_at'], $restaurantData['updated_at']);

        $response = $this->delete(route('admin.restaurants.destroy', $restaurant));
        $this->assertDatabaseHas('restaurants', $restaurantData);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を削除できない
    public function test_general_user_cannot_destroy_restaurant()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $restaurantData = $restaurant->toArray();

        $restaurantData['lowest_price'] = (int) $restaurantData['lowest_price'];
        $restaurantData['highest_price'] = (int) $restaurantData['highest_price'];
        $restaurantData['seating_capacity'] = (int) $restaurantData['seating_capacity'];

        unset($restaurantData['created_at'], $restaurantData['updated_at']);

        $response = $this->actingAs($user, 'web')->delete(route('admin.restaurants.destroy', $restaurant));
        $this->assertDatabaseHas('restaurants', $restaurantData);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は店舗を削除できる
    public function test_admin_user_can_destroy_restaurant()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->delete(route('admin.restaurants.destroy', $restaurant));
        $this->assertDatabaseMissing('restaurants', $restaurant->toArray());
        $response->assertRedirect(route('admin.restaurants.index'));
    }
}
