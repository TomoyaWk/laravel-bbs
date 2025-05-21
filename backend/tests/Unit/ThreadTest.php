<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Thread;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Database\Seeders\ThreadSeeder;

use function PHPUnit\Framework\assertEquals;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        
        // シードを順番に実行（UserSeederを先に実行）
        $this->seed(UserSeeder::class);
        $this->seed(ThreadSeeder::class);
    }

    protected function testLogin(string $email): string | null
    {
        $user = User::where('email', $email)->first();
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        return $response->json('token');
    }

    /**
     * スレッド一覧取得
     */
    public function test_スレッド一覧取得(): void
    {   
        // ログイン処理
        $token = $this->testLogin('user1@example.com');
        
        $expected = Thread::all()->count();
        // スレッド一覧取得
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/threads');

        $response->assertStatus(200)->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'user_id',
                        'is_locked',
                        'view_count',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active'
                    ],
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
            
        assertEquals(
            $expected,
            $response->json('total')
        );
    }

    public function test_スレッド詳細取得(): void
    {
        // ログイン処理
        $token = $this->testLogin('user1@example.com');

        $expected = Thread::where('id', 1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/threads/1');

        $response->assertStatus(200)->assertJsonStructure([
            'id',
            'title',
            'user_id',
            'is_locked',
            'view_count',
            'created_at',
            'updated_at',
        ]);
        assertEquals(
            $expected->id,
            $response->json('id')
        );
        assertEquals(
            $expected->title,
            $response->json('title')
        );
        assertEquals(
            $expected->user_id,
            $response->json('user_id')
        );
        assertEquals(
            $expected->is_locked,
            $response->json('is_locked')
        );
        assertEquals(
            $expected->view_count,
            $response->json('view_count')
        );
        assertEquals(
            $expected->created_at->toJSON(),
            $response->json('created_at')
        );
        assertEquals(
            $expected->updated_at->toJSON(),
            $response->json('updated_at')
        );
    }

    public function test_スレッド作成(): void
    {
        // ログイン処理
        $token = $this->testLogin('user1@example.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/threads', [
            'title' => '新しいスレッド',
            'user_id' => 1,
            'is_locked' => false,
            'view_count' => 0,
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            'id',
            'title',
            'user_id',
            'is_locked',
            'view_count',
            'created_at',
            'updated_at',
        ]);
        assertEquals(
            '新しいスレッド',
            $response->json('title')
        );
        assertEquals(
            1,
            $response->json('user_id')
        );
        assertEquals(
            false,
            $response->json('is_locked')
        );
        assertEquals(
            0,
            $response->json('view_count')
        );
    }

    public function test_スレッド更新(): void
    {
        // ログイン処理
        $token = $this->testLogin('user1@example.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/threads/1', [
            'title' => '更新されたスレッド',
            'user_id' => 1,
            'is_locked' => true,
            'view_count' => 10,
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'id',
            'title',
            'user_id',
            'is_locked',
            'view_count',
            'created_at',
            'updated_at',
        ]);
        assertEquals(
            1,
            $response->json('id')
        );
        assertEquals(
            '更新されたスレッド',
            $response->json('title')
        );
    }

    public function test_スレッド更新_権限エラー(): void
    {
        // ログイン処理
        $token = $this->testLogin('user2@example.com');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/threads/1', [
            'title' => '更新されたスレッド',
            'user_id' => 1,
            'is_locked' => true,
            'view_count' => 10,
        ]);
        $response->assertStatus(403)->assertJson([
            'error' => 'Unauthorized',
        ]);
    }

    public function test_スレッド削除(): void
    {
        // ログイン処理
        $token = $this->testLogin('user1@example.com');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/threads/1');

        $response->assertStatus(204);

        // 削除されたことを確認
        $this->assertDatabaseMissing('threads', [
            'id' => 1,
        ]);
    }

    public function test_スレッド削除_権限エラー(): void
    {
        // ログイン処理
        $token = $this->testLogin('user2@example.com');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/threads/1');
        $response->assertStatus(403)->assertJson([
            'error' => 'Unauthorized',
        ]);
        // 削除されていないことを確認
        $this->assertDatabaseHas('threads', [
            'id' => 1,
        ]); 
    }
}
