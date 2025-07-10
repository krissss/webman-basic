<?php

namespace app\api\controller\form\common;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationTrait
{
    /**
     * 分页大小
     */
    public int $limit = 10;

    /**
     * 当前页
     */
    public int $page = 1;

    protected function getWithPage(Builder $query): LengthAwarePaginator
    {
        return $query->paginate($this->limit, ['*'], 'page', $this->page);
    }
}
