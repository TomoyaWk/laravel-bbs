<?php

namespace App\Services\Queries;

use App\Models\Thread;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Requests\ThreadRequest;

/**
 * Class ThreadQueryBuilder
 * @package App\Services\Queries
 *
 * スレッドクエリビルダー
 * スレッド絞り込み用のリクエストを処理するクラス
 */
class ThreadQueryBuilder
{
    protected Builder $query;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->query = Thread::query();
    }

    public function build(): Builder
    {
        return $this->applyTitleFilter()
            ->applyUserFilter()
            ->applyLockFilter()
            ->applyViewCountFilter()
            ->applySorting()
            ->getQuery();
    }

    protected function applyTitleFilter(): self
    {
        if ($this->request->has('title')) {
            $this->query->where('title', 'like', '%' . $this->request->title . '%');
        }
        return $this;
    }

    protected function applyUserFilter(): self
    {
        if ($this->request->has('user_id')) {
            $this->query->where('user_id', $this->request->user_id);
        }
        return $this;
    }

    protected function applyLockFilter(): self
    {
        if ($this->request->has('is_locked')) {
            $this->query->where('is_locked', $this->request->boolean('is_locked'));
        }
        return $this;
    }

    protected function applyViewCountFilter(): self
    {
        if ($this->request->has('view_count_min')) {
            $this->query->where('view_count', '>=', $this->request->view_count_min);
        }

        if ($this->request->has('view_count_max')) {
            $this->query->where('view_count', '<=', $this->request->view_count_max);
        }
        return $this;
    }

    protected function applySorting(): self
    {
        $sortBy = $this->request->input('sort_by', 'created_at');
        $sortOrder = $this->request->input('sort_order', 'desc');
        
        $allowedSortFields = ['title', 'created_at', 'updated_at', 'view_count'];
        if (in_array($sortBy, $allowedSortFields)) {
            $this->query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $this->query->orderBy('created_at', 'desc');
        }
        return $this;
    }

    protected function getQuery(): Builder
    {
        return $this->query;
    }
}