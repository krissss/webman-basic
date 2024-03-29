<?php

namespace app\bootstrap;

use Illuminate\Pagination\LengthAwarePaginator;
use Webman\Bootstrap;

class Paginator implements Bootstrap
{
    public static function start($worker)
    {
        // 修改分页返回的结构
        \container()->bind(LengthAwarePaginator::class, fn ($app, array $options) => new class ($options['items'], $options['total'], $options['perPage'], $options['currentPage'], $options['options']) extends LengthAwarePaginator {
            /**
             * {@inheritDoc}
             */
            public function toArray()
            {
                return [
                    'items' => $this->items->toArray(),
                    'total' => $this->total(),
                ];
            }
        });
    }
}
