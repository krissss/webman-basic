<?php

namespace app\components;

use WebmanTech\DTO\BaseResponseDTO;

class ResponseLayout extends BaseResponseDTO
{
    public function __construct(
        public int    $code,
        public string $msg,
        public        $data,
    )
    {
    }
}
