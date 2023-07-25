<?php

namespace app\components\fileUpload\amis;

use app\components\fileUpload\FileUpload;

/**
 * 单个上传.
 *
 * @see https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-file#%E5%9F%BA%E6%9C%AC%E7%94%A8%E6%B3%95
 */
class SingleFileUpload extends FileUpload
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadForAmis(): array
    {
        return [
            'value' => $this->upload(),
        ];
    }
}
