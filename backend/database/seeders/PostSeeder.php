<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $threads = Thread::all();
        $users = User::all();
        
        // 各スレッドに投稿を作成
        foreach ($threads as $thread) {
            // 親投稿（ルート投稿）を5〜10個作成
            $rootPostCount = rand(5, 10);
            
            for ($i = 0; $i < $rootPostCount; $i++) {
                $rootPost = Post::create([
                    'thread_id' => $thread->id,
                    'user_id' => $users->random()->id,
                    'content' => "これはスレッド「{$thread->title}」の投稿 #" . ($i + 1) . " です。\n\n" .
                                "サンプル投稿の本文です。複数行のテキストが含まれています。\n" .
                                "このように表示されます。",
                    'parent_post_id' => null, // ルート投稿
                ]);
                
                // 50%の確率で返信を作成
                if (rand(0, 1) == 1) {
                    $replyCount = rand(1, 3);
                    
                    for ($j = 0; $j < $replyCount; $j++) {
                        $reply = Post::create([
                            'thread_id' => $thread->id,
                            'user_id' => $users->random()->id,
                            'content' => "これは投稿 #{$rootPost->id} への返信 #" . ($j + 1) . " です。",
                            'parent_post_id' => $rootPost->id, // 親投稿へのリンク
                        ]);
                        
                        // 25%の確率で返信の返信を作成
                        if (rand(0, 3) == 0) {
                            Post::create([
                                'thread_id' => $thread->id,
                                'user_id' => $users->random()->id,
                                'content' => "これは返信 #{$reply->id} への返信です。ツリー構造の例です。",
                                'parent_post_id' => $reply->id, // 返信への返信
                            ]);
                        }
                    }
                }
            }
        }
    }
}
