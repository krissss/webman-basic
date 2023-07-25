<?php

namespace app\enums\traits;

trait EnumLabelSupport
{
    /**
     * 获取带 label 形式的 items.
     */
    public static function getViewLabeledItems(): array
    {
        $data = [];
        foreach (static::getViewItems() as $value => $description) {
            $label = static::getLabel($value);
            $description = $label ? "<span class='label label-{$label}'>{$description}</span>" : $description;
            $data[$value] = $description;
        }

        return $data;
    }

    /**
     * 获取 label.
     */
    public static function getLabel($value): ?string
    {
        return static::getViewLabels()[$value] ?? 'default';
    }

    /**
     * 配置 label
     * $value => 'info'
     * 支持：info/success/danger/warning/default.
     */
    public static function getViewLabels(): array
    {
        return [];
    }
}
