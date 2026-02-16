<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Spatie\MarkdownResponse\Events\MarkdownCacheHitEvent;
use Spatie\MarkdownResponse\Middleware\ProvideMarkdownResponse;

it('caches converted markdown', function () {
    config()->set('markdown-response.cache.enabled', true);
    config()->set('markdown-response.cache.store', 'array');

    $middleware = new ProvideMarkdownResponse;
    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');

    $callCount = 0;
    $next = function () use (&$callCount) {
        $callCount++;

        return new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);
    };

    $middleware->handle($request, $next);
    $middleware->handle($request, $next);

    expect($callCount)->toBe(1);
});

it('fires cache hit event', function () {
    Event::fake();
    config()->set('markdown-response.cache.enabled', true);
    config()->set('markdown-response.cache.store', 'array');

    $middleware = new ProvideMarkdownResponse;
    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');

    $next = fn () => new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);

    $middleware->handle($request, $next);
    $middleware->handle($request, $next);

    Event::assertDispatched(MarkdownCacheHitEvent::class);
});

it('skips cache when disabled', function () {
    config()->set('markdown-response.cache.enabled', false);

    $middleware = new ProvideMarkdownResponse;
    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');

    $callCount = 0;
    $next = function () use (&$callCount) {
        $callCount++;

        return new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);
    };

    $middleware->handle($request, $next);
    $middleware->handle($request, $next);

    expect($callCount)->toBe(2);
});
