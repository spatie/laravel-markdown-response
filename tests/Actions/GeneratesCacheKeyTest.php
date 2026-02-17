<?php

use Illuminate\Http\Request;
use Spatie\MarkdownResponse\Actions\GeneratesCacheKey;

it('generates a cache key from the request', function () {
    $request = Request::create('https://example.com/about');
    $generator = new GeneratesCacheKey;

    $key = $generator($request);

    expect($key)->toStartWith('markdown-response:');
});

it('strips md suffix from cache key', function () {
    $generator = new GeneratesCacheKey;

    $withSuffix = $generator(Request::create('https://example.com/about.md'));
    $withoutSuffix = $generator(Request::create('https://example.com/about'));

    expect($withSuffix)->toBe($withoutSuffix);
});

it('ignores utm parameters', function () {
    config()->set('markdown-response.cache.ignored_query_parameters', ['utm_source', 'utm_medium']);

    $generator = new GeneratesCacheKey;

    $withUtm = $generator(Request::create('https://example.com/about?utm_source=google&utm_medium=cpc'));
    $withoutUtm = $generator(Request::create('https://example.com/about'));

    expect($withUtm)->toBe($withoutUtm);
});

it('includes relevant query parameters', function () {
    $generator = new GeneratesCacheKey;

    $withPage = $generator(Request::create('https://example.com/about?page=2'));
    $withoutPage = $generator(Request::create('https://example.com/about'));

    expect($withPage)->not->toBe($withoutPage);
});

it('produces consistent keys regardless of query parameter order', function () {
    $generator = new GeneratesCacheKey;

    $keyA = $generator(Request::create('https://example.com/about?a=1&b=2'));
    $keyB = $generator(Request::create('https://example.com/about?b=2&a=1'));

    expect($keyA)->toBe($keyB);
});
