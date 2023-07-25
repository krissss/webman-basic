<?php

namespace app\model\traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @see https://learnku.com/laravel/t/16945
 *
 * @method static Builder|static like(string $column, string $value, string $side = 'both', bool $isNotLike = false, bool $isAnd = true)
 * @method static Builder|static orLike(string $column, string $value, string $side = 'both', bool $isNotLike = false)
 * @method static Builder|static notLike(string $column, string $value, string $side = 'both', bool $isAnd = true)
 * @method static Builder|static orNotLike(string $column, string $value, string $side = 'both')
 */
trait LikeScopedTrait
{
    /**
     * 是否转译 like 的查询值
     * 在 mysql 下需要转译.
     */
    protected bool $escapeLike = true;

    public function scopeLike(Builder $query, string $column, string $value, string $side = 'both', bool $isNotLike = false, bool $isAnd = true): Builder
    {
        $operator = $isNotLike ? 'not like' : 'like';

        $escape_like_str = function ($str) {
            if (!$this->escapeLike) {
                return $str;
            }
            $like_escape_char = '\\';

            return str_replace([$like_escape_char, '%', '_'], [
                $like_escape_char.$like_escape_char,
                $like_escape_char.'%',
                $like_escape_char.'_',
            ], $str);
        };

        switch ($side) {
            case 'none':
                $value = $escape_like_str($value);
                break;
            case 'before':
            case 'left':
                $value = "%{$escape_like_str($value)}";
                break;
            case 'after':
            case 'right':
                $value = "{$escape_like_str($value)}%";
                break;
            case 'both':
            case 'all':
            default:
                $value = "%{$escape_like_str($value)}%";
                break;
        }

        return $isAnd ? $query->where($column, $operator, $value) : $query->orWhere($column, $operator, $value);
    }

    public function scopeOrLike(Builder $query, string $column, string $value, string $side = 'both', bool $isNotLike = false): Builder
    {
        return $query->like($column, $value, $side, $isNotLike, false);
    }

    public function scopeNotLike(Builder $query, string $column, string $value, string $side = 'both', bool $isAnd = true): Builder
    {
        return $query->like($column, $value, $side, true, $isAnd);
    }

    public function scopeOrNotLike(Builder $query, string $column, string $value, string $side = 'both'): Builder
    {
        return $query->like($column, $value, $side, true, false);
    }
}
