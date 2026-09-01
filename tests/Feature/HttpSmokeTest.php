<?php

test('GET / returns hello message', function () {
    $this->getJson('/')->assertOk()->assertSee('Hello');
});

test('unknown route returns 404 in json body', function () {
    // 项目约定：错误经异常处理器返回，HTTP 恒 200，业务状态在 body 内表达
    $response = $this->getJson('/not-exist');
    $response->assertOk()->assertSee('404 Not Found');
});

test('admin protected route requires authentication', function () {
    $response = $this->getJson('/admin/info');
    $response->assertOk();
    expect($response->json('status'))->toBe(401);
});
