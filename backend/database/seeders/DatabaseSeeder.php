<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 順番が重要です。UserSeeder→ThreadSeeder→PostSeederの順で実行
        $this->call([
            UserSeeder::class,    // まずユーザーを作成
            ThreadSeeder::class,  // 次にスレッドを作成
            PostSeeder::class,    // 最後に投稿を作成
        ]);
    }
}
