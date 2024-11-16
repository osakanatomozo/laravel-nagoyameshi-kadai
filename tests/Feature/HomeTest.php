<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインのユーザーは会員側のトップページにアクセスできる
    public function test_guest_user_can_access_home()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側のトップページにアクセスできる
    public function test_general_user_can_access_home()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のトップページにアクセスできない
    public function test_admin_user_cannot_access_home()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);

        $response = $this->actingAs($adminUser, 'admin')->get(route('home'));
        $response->assertRedirect(route('admin.home'));
    }
}
