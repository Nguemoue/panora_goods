<?php

test('can generate a product code', function () {
    $generator = new \App\Services\ProductCodeGenerator();
    $code = $generator->generate('Test Product');
    dump($code);
    expect($code)->toBeString();
    expect(strlen($code))->toBeGreaterThan(0);
});

