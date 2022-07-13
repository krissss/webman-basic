<?php

namespace app\model\traits;

trait ModelEnumSupport
{
    /**
     * @return array
     */
    public static function getViewItems(): array
    {
        return static::query()->get()->pluck('name', 'id');
    }

    /**
     * @return array
     */
    public static function getLabelValue(): array
    {
        $data = [];
        foreach (static::getViewItems() as $value => $label) {
            $data[] = [
                'label' => $label,
                'value' => $value,
            ];
        }
        return $data;
    }
}
