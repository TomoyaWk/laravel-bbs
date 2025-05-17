<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Thread;
use App\Models\User;

use function PHPUnit\Framework\assertEquals;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // testData豆乳
    }

    /**
     * スレッド一覧取得
     */
    public function test_スレッド一覧取得(): void
    {   
        // ログイン処理
        $user = User::where('email', 'user1@example.com')->first();
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $loginResponse->assertStatus(200);
        $token = $loginResponse->json('token');
        

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
        $user = User::where('email', 'user1@example.com')->first();
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $loginResponse->assertStatus(200);

        $expected = Thread::where('id', 1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $loginResponse->json('token'),
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
            $expected->created_at,
            $response->json('created_at')
        );
        assertEquals(
            $expected->updated_at,
            $response->json('updated_at')
        );
    }
}

