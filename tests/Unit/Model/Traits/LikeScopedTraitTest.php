<?php

use app\model\traits\LikeScopedTrait;
use Illuminate\Database\Eloquent\Model;

final class LikeScopedTraitTestModel extends Model
{
    use LikeScopedTrait;

    protected $table = 'users';
}

test('like adds a both-side escaped like condition by default', function () {
    $query = LikeScopedTraitTestModel::query()->like('name', 'a%b_c\\d');

    expect($query->toSql())->toBe('select * from "users" where "name" like ?')
        ->and($query->getBindings())->toBe(['%a\%b\_c\\\\d%']);
});

test('like supports side options', function (string $side, string $expected) {
    $query = LikeScopedTraitTestModel::query()->like('name', 'foo', $side);

    expect($query->getBindings())->toBe([$expected]);
})->with([
    'none' => ['none', 'foo'],
    'before' => ['before', '%foo'],
    'left' => ['left', '%foo'],
    'after' => ['after', 'foo%'],
    'right' => ['right', 'foo%'],
    'both' => ['both', '%foo%'],
    'all' => ['all', '%foo%'],
]);

test('orLike uses or where with escaped like condition', function () {
    $query = LikeScopedTraitTestModel::query()
        ->where('id', 1)
        ->orLike('name', 'foo');

    expect($query->toSql())->toBe('select * from "users" where "id" = ? or (("name" like ?))')
        ->and($query->getBindings())->toBe([1, '%foo%']);
});

test('notLike and orNotLike use not like operator', function () {
    $notLikeQuery = LikeScopedTraitTestModel::query()->notLike('name', 'foo');
    $orNotLikeQuery = LikeScopedTraitTestModel::query()
        ->where('id', 1)
        ->orNotLike('name', 'foo');

    expect($notLikeQuery->toSql())->toBe('select * from "users" where "name" not like ?')
        ->and($notLikeQuery->getBindings())->toBe(['%foo%'])
        ->and($orNotLikeQuery->toSql())->toBe('select * from "users" where "id" = ? or (("name" not like ?))')
        ->and($orNotLikeQuery->getBindings())->toBe([1, '%foo%']);
});
