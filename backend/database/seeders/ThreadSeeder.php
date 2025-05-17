<?php

namespace Database\Seeders;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Seeder;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->call(UserSeeder::class);
            $users = User::all();
        }

        $userId = $users->first()->id;

        // サンプルスレッドを作成
        Thread::create([
            'title' => '掲示板システムの使い方',
            'user_id' => $userId, // 管理者が作成
            'view_count' => 100,
            'is_locked' => false,
        ]);
        
        Thread::create([
            'title' => '自己紹介スレッド',
            'user_id' => $userId, // テストユーザー1が作成
            'view_count' => 50,
            'is_locked' => false,
        ]);
        
        Thread::create([
            'title' => 'お知らせ（ロック済み）',
            'user_id' => $userId, // 管理者が作成
            'view_count' => 80,
            'is_locked' => true,
        ]);
        
        // ランダムなスレッドを作成
        for ($i = 0; $i < 10; $i++) {
            Thread::create([
                'title' => 'サンプルスレッド ' . ($i + 1),
                'user_id' => $users->random()->id,
                'view_count' => rand(0, 100),
                'is_locked' => rand(0, 10) > 9, // 10%の確率でロック
            ]);
        }
    }
}
