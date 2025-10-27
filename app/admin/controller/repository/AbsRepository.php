<?php

namespace app\admin\controller\repository;

use app\enums\common\OnOffStatusEnum;
use WebmanTech\AmisAdmin\Amis\FormField;
use WebmanTech\AmisAdmin\Amis\GridColumn;
use WebmanTech\AmisAdmin\Helper\DTO\PresetItem;
use WebmanTech\AmisAdmin\Helper\PresetsHelper;
use WebmanTech\AmisAdmin\Repository\EloquentRepository;
use WebmanTech\AmisAdmin\Repository\HasPresetInterface;
use WebmanTech\AmisAdmin\Repository\HasPresetTrait;

abstract class AbsRepository extends EloquentRepository implements HasPresetInterface
{
    use HasPresetTrait;

    protected function createPresetsHelper(): PresetsHelper
    {
        return (new PresetsHelper())
            ->withPresets([
                'id' => new PresetItem(
                    label: 'ID',
                    form: false,
                ),
                'name' => new PresetItem(
                    label: '名称',
                    filter: 'like',
                    rule: 'required|string',
                ),
                'status' => new PresetItem(
                    label: '状态',
                    gridExt: fn(GridColumn $column) => $column->quickEdit(),
                    selectOptions: fn() => OnOffStatusEnum::getViewLabeledItems(),
                    formDefaultValue: fn() => OnOffStatusEnum::On->value,
                ),
                'remark' => new PresetItem(
                    label: '备注',
                    filter: 'like',
                    gridExt: fn(GridColumn $column) => $column->truncate(),
                    formExt: fn(FormField $field) => $field->typeTextarea(),
                ),
                'created_at' => new PresetItem(
                    label: '创建时间',
                    filter: 'datetime-range',
                    gridExt: fn(GridColumn $column) => $column->sortable()->searchable([
                        'type' => 'input-datetime-range',
                    ]),
                    form: false,
                ),
                'updated_at' => new PresetItem(
                    label: '更新时间',
                    filter: 'datetime-range',
                    gridExt: fn(GridColumn $column) => $column->sortable()->searchable([
                        'type' => 'input-datetime-range',
                    ])->toggled(false),
                    form: false,
                ),
                'sort' => new PresetItem(
                    label: '排序',
                    filter: false,
                    gridExt: fn(GridColumn $column) => $column->quickEdit(),
                    formExt: fn(FormField $field) => $field->typeInputNumber(),
                ),
            ]);
    }
}
