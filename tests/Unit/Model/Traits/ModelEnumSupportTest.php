<?php

use app\model\traits\ModelEnumSupport;
use Illuminate\Support\Collection;

test('it builds view item and label value arrays from models', function () {
    $model = new class {
        use ModelEnumSupport;

        protected static function viewItemsCollection(): Collection
        {
            return collect([
                ['id' => 1, 'name' => '启用'],
                ['id' => 2, 'name' => '禁用'],
            ]);
        }
    };

    expect($model::getViewItems())->toBe([
        1 => '启用[1]',
        2 => '禁用[2]',
    ])->and($model::getLabelValue())->toBe([
        ['label' => '启用[1]', 'value' => 1],
        ['label' => '禁用[2]', 'value' => 2],
    ]);
});
