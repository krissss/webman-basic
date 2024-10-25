<?php

namespace app\api\controller\schema\traits;

use OpenApi\Attributes as OA;

#[OA\Schema]
trait PaginationTrait
{
    #[OA\Property(description: '分页', example: 10, nullable: true)]
    public int $limit = 10;

    #[OA\Property(description: '当前页', example: 1, nullable: true)]
    public int $page = 1;
}
