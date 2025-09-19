<?php

use app\components\ResponseLayout;
use WebmanTech\DTO\BaseResponseDTO;

return [
    'to_response_format' => function (BaseResponseDTO $responseDTO) {
        return ResponseLayout::fromResponseDTO($responseDTO)
            ->useToArrayForData(true)
            ->useStatusCode(false)
            ->toJsonResponse();
    }
];
