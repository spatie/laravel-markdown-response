<?php

use Spatie\MarkdownResponse\Facades\Markdown;

it('can fake the converter', function () {
    $fake = Markdown::fake();

    $result = Markdown::convert('<h1>Hello</h1>');

    expect($result)->toBe('# Fake Markdown Response');
    $fake->assertConverted();
});

it('can assert no conversions were made', function () {
    $fake = Markdown::fake();

    $fake->assertNotConverted();
});

it('can assert conversion count', function () {
    $fake = Markdown::fake();

    Markdown::convert('<h1>One</h1>');
    Markdown::convert('<h1>Two</h1>');
    Markdown::convert('<h1>Three</h1>');

    $fake->assertConvertedCount(3);
});

it('can assert with a callback', function () {
    $fake = Markdown::fake();

    Markdown::convert('<h1>Hello</h1>');

    $fake->assertConverted(fn (string $html) => str_contains($html, 'Hello'));
});

it('fails assertion when callback does not match', function () {
    $fake = Markdown::fake();

    Markdown::convert('<h1>Hello</h1>');

    expect(fn () => $fake->assertConverted(
        fn (string $html) => str_contains($html, 'Goodbye'),
    ))->toThrow(PHPUnit\Framework\ExpectationFailedException::class);
});
