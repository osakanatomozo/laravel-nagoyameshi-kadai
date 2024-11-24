<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use App\Models\Admin;
use App\Models\Subscribed;

class ReviewTest extends TestCase
{

    use RefreshDatabase;

    // indexアクション（レビュー一覧ページ）
    // 未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
    public function test_guest_cannot_access_reviews_index()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->get(route('restaurants.reviews.index',$review));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_general_user_can_access_reviews_index()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($user)->get(route('restaurants.reviews.index',$review));
        $response->assertStatus(200);
    }

    // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_premium_user_can_access_reviews_index()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($user)->get(route('restaurants.reviews.index',$review));
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    public function test_admin_cannot_access_reviews_index()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.index',$review));
        $response->assertRedirect(route('admin.home'));
    }

    // createアクション（レビュー投稿ページ）
    // 未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    public function test_guest_cannot_access_reviews_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->get(route('restaurants.reviews.create',$review));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
    public function test_general_user_cannot_access_reviews_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($user)->get(route('restaurants.reviews.create',$review));
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
    public function test_premium_user_can_access_reviews_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($user)->get(route('restaurants.reviews.create',$review));
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
    public function test_admin_cannot_access_reviews_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.create',$review));
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（レビュー投稿機能）
    // 未ログインのユーザーはレビューを投稿できない
    public function test_guest_cannot_store_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reviewData = [
            'score' => 1,
            'content' => 'テスト',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];

        $response = $this->post(route('restaurants.reviews.store', $restaurant), $reviewData);
        $this->assertDatabaseMissing('reviews', $reviewData);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを投稿できない
    public function test_general_user_cannot_store_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reviewData = [
            'score' => 1,
            'content' => 'テスト',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];

        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), $reviewData);
        $this->assertDatabaseMissing('reviews', $reviewData);
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はレビューを投稿できる
    public function test_premium_user_can_store_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');
        $reviewData = [
            'score' => 1,
            'content' => 'テスト',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];


        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), $reviewData);
        $this->assertDatabaseHas('reviews', $reviewData);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // // ログイン済みの管理者はレビューを投稿できない
    public function test_admin_cannot_store_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $reviewData = [
            'score' => 1,
            'content' => 'テスト',
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ];


        $response = $this->actingAs($admin, 'admin')->post(route('restaurants.reviews.store', $restaurant), $reviewData);
        $this->assertDatabaseMissing('reviews', $reviewData);
        $response->assertRedirect(route('admin.home'));
    }

    // editアクション（レビュー編集ページ）
    // 未ログインのユーザーは会員側のレビュー編集ページにアクセスできない
    public function test_guest_cannot_access_reviews_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->get(route('restaurants.reviews.edit', [
            'restaurant' => $restaurant,
            'review' => $review,
        ]));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビュー編集ページにアクセスできない
    public function test_general_user_cannot_access_reviews_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit',[
            'restaurant' => $restaurant,
            'review' => $review,
        ]));
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
    public function test_premium_user_cannot_access_other_reviews_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $otherReview = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);


        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [
            'restaurant' => $restaurant,
            'review' => $otherReview,
        ]));
        $response->assertStatus(500);
    }

    // ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
    public function test_premium_user_can_access_own_reviews_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [
            'restaurant' => $restaurant,
            'review' => $review,
        ]));
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー編集ページにアクセスできない
    public function test_admin_cannot_access_reviews_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);


        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.edit', [
            'restaurant' => $restaurant,
            'review' => $review,
        ]));
        $response->assertRedirect(route('admin.home'));
    }

    // updateアクション（レビュー更新機能）
    // 未ログインのユーザーはレビューを更新できない
    public function test_guest_cannot_update_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $reviewData = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ]);

        $updateReviewData = [
            'score' => 1,
            'content' => 'テスト2',
        ];

        $response = $this->patch(route('restaurants.reviews.update', [$restaurant, $reviewData]), $updateReviewData);
        $this->assertDatabaseMissing('reviews', $updateReviewData);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを更新できない
    public function test_general_user_cannot_update_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reviewData = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ]);

        $updateReviewData = [
            'score' => 1,
            'content' => 'テスト2',
        ];

        $response = $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $reviewData]), $updateReviewData);
        $this->assertDatabaseMissing('reviews', $updateReviewData);
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は他人のレビューを更新できない
    public function test_premium_user_cannot_update_other_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');
        $otherReviewData = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $updateReviewData = [
            'score' => 1,
            'content' => 'テスト2',
        ];

        $response = $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $otherReviewData]), $updateReviewData);
        $this->assertDatabaseMissing('reviews', $updateReviewData);
        $response->assertStatus(500);
    }

    // ログイン済みの有料会員は自身のレビューを更新できる
    public function test_premium_user_can_update_own_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $reviewData = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $updateReviewData = [
            'score' => 1,
            'content' => 'テスト2',
        ];

        $response = $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $reviewData]), $updateReviewData);
        $this->assertDatabaseHas('reviews', $updateReviewData);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの管理者はレビューを更新できない
    public function test_premium_user_cannot_update_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $reviewData = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $updateReviewData = [
            'score' => 1,
            'content' => 'テスト2',
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('restaurants.reviews.update', [$restaurant, $reviewData]), $updateReviewData);
        $this->assertDatabaseMissing('reviews', $updateReviewData);
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（レビュー削除機能）
    // 未ログインのユーザーはレビューを削除できない
    public function test_guest_cannot_destroy_reviews()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はレビューを削除できない
    public function test_general_user_cannot_destroy_reviews()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員は他人のレビューを削除できない
    public function test_premium_user_cannot_destroy_other_reviews()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');
        $otherUser = User::factory()->create();
        $otherUser->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの有料会員は自身のレビューを削除できる
    public function test_premium_user_can_destroy_own_reviews()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    // ログイン済みの管理者はレビューを削除できない
    public function test_admin_cannot_destroy_reviews()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QNEDuFMjCpqJdkeqoMnaOGL')->create('pm_card_visa');

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($admin, 'admin')->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
        $response->assertRedirect(route('admin.home'));
    }
}
