<?php

namespace app\model\traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ModelEnumSupport
{
    protected static function viewItemsCollection(): Collection
    {
        return static::query()->get();
    }

    /**
     * @param Model $item
     */
    protected static function viewItemsMapping($item): array
    {
        return [$item['id'] => "{$item['name']}[{$item['id']}]"];
    }

    public static function getViewItems(): array
    {
        return static::viewItemsCollection()
            ->mapWithKeys(fn ($item) => static::viewItemsMapping($item))
            ->toArray();
    }

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
