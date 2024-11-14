<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Company;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション
    // 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_user_cannot_access_admin_company_index()
    {
        $company = Company::factory()->create();

        $response = $this->get(route('admin.company.index', $company));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_general_user_cannot_access_admin_company_index()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.index', $company));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    public function test_admin_user_can_access_admin_company_index()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $company = Company::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.company.index'));
        $response->assertStatus(200);
    }

    // editアクション
    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_guest_user_cannot_access_admin_company_edit()
    {
        $company = Company::factory()->create();

        $response = $this->get(route('admin.company.edit', $company));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_general_user_cannot_access_admin_company_edit()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.edit', $company));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
    public function test_admin_user_can_access_admin_company_edit()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $company = Company::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.company.edit', $company));
        $response->assertStatus(200);
    }

    // updateアクション
    // 未ログインのユーザーは会社概要を更新できない
    public function test_guest_user_cannot_update_company()
    {
        $company = Company::factory()->create();

        $update_company= [
            'name' => 'テスト2',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト',
        ];

        $response = $this->patch(route('admin.company.update', $company), $update_company);
        $this->assertDatabaseMissing('companies', $update_company);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは会社概要を更新できない
    public function test_general_user_cannot_update_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $update_company= [
            'name' => 'テスト2',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト',
        ];

        $response = $this->actingAs($user)->patch(route('admin.company.update', $company), $update_company);
        $this->assertDatabaseMissing('companies', $update_company);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は会社概要を更新できる
    public function test_admin_user_can_update_company()
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $company = Company::factory()->create();

        $update_company= [
            'name' => 'テスト2',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト',
        ];

        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.company.update', $company), $update_company);
        $this->assertDatabaseHas('companies', $update_company);
        $response->assertRedirect(route('admin.company.index'));
    }
}
