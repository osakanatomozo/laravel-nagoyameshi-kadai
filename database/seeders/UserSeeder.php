<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // UserFactoryクラスで定義した内容にもとづいてダミーデータを100つ生成し、usersテーブルに追加する
        // User::factory()->count(100)->create();

        $user = new User();
        $user->name = '侍 太郎';
        $user->kana = 'サムライ タロウ';
        $user->email = 'user@example.com';
        $user->password = Hash::make('user_nagoyameshi');
        $user->postal_code = '1050001';
        $user->address = '東京都港区虎ノ門１丁目３−１';
        $user->phone_number = '03-5790-9039';
        $user->save();
    }
}
