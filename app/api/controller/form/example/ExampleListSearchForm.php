<?php

namespace app\api\controller\form\example;

use app\api\controller\form\common\PaginationTrait;
use app\enums\common\OnOffStatusEnum;
use app\model\Admin as Model;
use WebmanTech\DTO\Attributes\ValidationRules;
use WebmanTech\DTO\BaseRequestDTO;
use WebmanTech\DTO\BaseResponseDTO;

final class ExampleListSearchForm extends BaseRequestDTO
{
    use PaginationTrait;

    /**
     * 用户名
     * @example admin
     */
    public string $username;

    /**
     * 状态
     */
    public ?OnOffStatusEnum $status = null;

    public function search(): ExampleListSearchFormResult
    {
        $query = Model::query();
        if ($value = $this->username) {
            $query->where('username', $value);
        }
        if ($value = $this->status) {
            $query->where('status', $value);
        }

        $paginator = $this->getWithPage($query);
        return new ExampleListSearchFormResult(
            count: $paginator->total(),
            list: $paginator->items(),
        );
    }
}

final class ExampleListSearchFormResult extends BaseResponseDTO
{
    public function __construct(
        public int   $count,
        #[ValidationRules(arrayItem: Model::class)]
        public array $list,
    )
    {
    }
}
