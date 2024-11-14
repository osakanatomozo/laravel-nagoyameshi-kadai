<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Term;

class TermTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（利用規約ページ）
    // 未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_guest_user_cannot_access_admin_term_index()
    {
        $term = Term::factory()->create();

        $response = $this->get(route('admin.terms.index', $term));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_general_user_cannot_access_admin_term_index()
    {
        $user = User::factory()->create();
        $term = Term::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.terms.index', $term));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_admin_user_can_access_admin_term_index()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $term = Term::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.terms.index'));
        $response->assertStatus(200);
    }

    // editアクション（利用規約編集ページ）
    // 未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_guest_user_cannot_access_admin_term_edit()
    {
        $term = Term::factory()->create();

        $response = $this->get(route('admin.terms.edit', $term));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_general_user_cannot_access_admin_term_edit()
    {
        $user = User::factory()->create();
        $term = Term::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.terms.edit', $term));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_admin_user_can_access_admin_term_edit()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $term = Term::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.terms.edit', $term));
        $response->assertStatus(200);
    }

    // updateアクション（利用規約更新機能）
    // 未ログインのユーザーは利用規約を更新できない
    public function test_guest_user_cannot_update_term()
    {
        $term = Term::factory()->create();

        $update_term= [
            'content' => 'テスト２',
        ];

        $response = $this->patch(route('admin.terms.update', $term), $update_term);
        $this->assertDatabaseMissing('terms', $update_term);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは利用規約を更新できない
    public function test_general_user_cannot_update_term()
    {
        $user = User::factory()->create();
        $term = Term::factory()->create();

        $update_term= [
            'content' => 'テスト２',
        ];

        $response = $this->actingAs($user)->patch(route('admin.terms.update', $term), $update_term);
        $this->assertDatabaseMissing('terms', $update_term);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は利用規約を更新できる
    public function test_admin_user_can_update_term()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $term = Term::factory()->create();

        $update_term= [
            'content' => 'テスト２',
        ];

        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.terms.update', $term), $update_term);
        $this->assertDatabaseHas('terms', $update_term);
        $response->assertRedirect(route('admin.terms.index'));
    }

}
