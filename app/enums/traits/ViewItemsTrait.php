<?php

namespace app\enums\traits;

use app\enums\EnumCache;

trait ViewItemsTrait
{
    /**
     * 获取 name => desc 形式的选项
     * @return array<int|string, string>
     */
    public static function getViewItems(): array
    {
        return EnumCache::getOrSetViewItems(__CLASS__, function () {
            $data = [];
            foreach (self::cases() as $case) {
                $desc = null;
                /** @phpstan-ignore-next-line */
                if (method_exists($case, 'description')) {
                    $desc = $case->description();
                }
                $data[$case->value] = $desc ?? $case->name;
            }
            return $data;
        });
    }

    /**
     * 获取带 label 形式的 items
     * @return array<int|string, string>
     */
    public static function getViewLabeledItems(): array
    {
        $data = [];
        foreach (self::getViewItems() as $value => $desc) {
            $case = self::from($value);
            $label = 'default';
            /** @phpstan-ignore-next-line */
            if (method_exists($case, 'viewLabel')) {
                /** @phpstan-ignore-next-line */
                $label = $case->viewLabel() ?? $label; // info/success/danger/warning/default
            }
            $data[$value] = "<span class='label label-{$label}'>{$desc}</span>";
        }
        return $data;
    }
}
