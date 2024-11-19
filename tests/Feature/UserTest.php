<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（会員情報ページ）
    // 未ログインのユーザーは会員側の会員情報ページにアクセスできない
    public function test_guest_cannot_access_user_index()
    {
        $response = $this->get(route('user.index'));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
    public function test_general_user_can_access_users_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.index'));
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
    public function test_admin_user_cannot_access_users_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('user.index'));

        $response->assertRedirect(route('admin.home'));
    }


    // editアクション（会員情報編集ページ）
    // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
    public function test_guest_user_cannot_access_users_edit()
    {
        $user = User::factory()->create();

        $response = $this->get(route('user.edit', $user));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_general_user_cannot_access_others_edit()
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.edit', $other_user));
        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_general_user_can_access_users_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.edit', $user));
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_user_cannot_access_users_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('user.edit', $user));
        $response->assertRedirect(route('admin.home'));
    }

    // updateアクション（会員情報更新機能）
    // 未ログインのユーザーは会員情報を更新できない
    public function test_guest_user_cannot_update_user_info()
    {
        $user = User::factory()->create();

        $updateUser = [
            'name' => 'テスト2',
            'kana' => 'テストツー',
            'email' => 'test2@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト2',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト2'
        ];

        $response = $this->patch(route('user.update', $user), $updateUser);

        $this->assertDatabaseMissing('users', $updateUser);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは他人の会員情報を更新できない
    public function test_general_user_cannot_update_user_info()
    {

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $updateOtherUser = [
            'name' => 'テスト2',
            'kana' => 'テストツー',
            'email' => 'test2@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト2',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト2'
        ];

        $response = $this->actingAs($user)->patch(route('user.update', $otherUser), $updateOtherUser);

        $this->assertDatabaseMissing('users', $updateOtherUser);
        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの一般ユーザーは自身の会員情報を更新できる
    public function test_general_user_can_update_user_info()
    {
        $user = User::factory()->create();

        $updateUser = [
            'name' => 'テスト2',
            'kana' => 'テストツー',
            'email' => 'test2@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト2',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト2'
        ];

        $response = $this->actingAs($user)->patch(route('user.update', $user), $updateUser);

        $this->assertDatabaseHas('users', $updateUser);
        $response->assertRedirect(route('user.index'));
    }

    // ログイン済みの管理者は会員情報を更新できない
    public function test_admin_user_cannot_update_user_info()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $user = User::factory()->create();

        $updateUser = [
            'name' => 'テスト2',
            'kana' => 'テストツー',
            'email' => 'test2@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト2',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト2'
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('user.update', $user), $updateUser);
        $this->assertDatabaseMissing('users', $updateUser);
        $response->assertRedirect(route('admin.home'));
    }
}
