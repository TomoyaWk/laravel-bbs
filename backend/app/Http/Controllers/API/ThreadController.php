<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;
use App\Services\Queries\ThreadQueryBuilder;
use App\Http\Requests\ThreadRequest;

class ThreadController extends Controller
{
    /**
     * スレッド一覧取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse {
        $queryBuilder = new ThreadQueryBuilder($request);
        $query = $queryBuilder->build();
        
        $perPage = $request->input('per_page', 15);
        $threads = $query->paginate($perPage);

        return response()->json($threads);
    }

    /**
     * スレッド詳細取得
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function get(Request $request, int $id): JsonResponse {
        $thread = Thread::find($id);
        if (!$thread) {
            return response()->json(['error' => 'Thread not found'], 404);
        }
        return response()->json($thread);
    }

    /**
     * スレッド作成
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(ThreadRequest $request): JsonResponse {
        $thread = Thread::create($request->all());
        return response()->json($thread, 201);
    }

    /**
     * スレッド更新
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ThreadRequest $request, int $id): JsonResponse {
        $thread = Thread::find($id);
        if (!$thread) {
            return response()->json(['error' => 'Thread not found'], 404);
        }
        //作成者のみ更新可能
        if ($thread->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        $thread->update($request->all());
        return response()->json($thread);
    }

    /**
     * スレッド削除
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id): JsonResponse {
        $thread = Thread::find($id);
        if (!$thread) {
            return response()->json(['error' => 'Thread not found'], 404);
        }
        //作成者のみ削除可能
        if ($thread->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $thread->delete();
        return response()->json(null, 204);
    }
}
