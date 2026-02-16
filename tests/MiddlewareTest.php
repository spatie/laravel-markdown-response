<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Spatie\MarkdownResponse\Events\ConvertedToMarkdownEvent;
use Spatie\MarkdownResponse\Events\ConvertingToMarkdownEvent;
use Spatie\MarkdownResponse\Middleware\DoNotProvideMarkdownResponse;
use Spatie\MarkdownResponse\Middleware\ProvideMarkdownResponse;

function handleMiddleware(Request $request, ?string $html = null, int $status = 200, string $contentType = 'text/html'): Response
{
    $middleware = new ProvideMarkdownResponse;

    $html ??= '<html><body><h1>Hello</h1><p>World</p></body></html>';

    return $middleware->handle($request, function () use ($html, $status, $contentType) {
        return new Response($html, $status, ['Content-Type' => $contentType]);
    });
}

it('converts html to markdown for accept header requests', function () {
    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');

    $response = handleMiddleware($request);

    expect($response->headers->get('Content-Type'))->toBe('text/markdown; charset=UTF-8');
    expect($response->getContent())->toContain('Hello');
    expect($response->getContent())->toContain('World');
});

it('converts html to markdown for md suffix requests', function () {
    $request = Request::create('/about.md');

    $response = handleMiddleware($request);

    expect($response->headers->get('Content-Type'))->toBe('text/markdown; charset=UTF-8');
});

it('converts html to markdown for ai user agent requests', function () {
    $request = Request::create('/about');
    $request->headers->set('User-Agent', 'ClaudeBot/1.0');

    $response = handleMiddleware($request);

    expect($response->headers->get('Content-Type'))->toBe('text/markdown; charset=UTF-8');
});

it('does not convert normal requests', function () {
    $request = Request::create('/about');
    $request->headers->set('User-Agent', 'Mozilla/5.0');

    $response = handleMiddleware($request);

    expect($response->headers->get('Content-Type'))->toBe('text/html');
});

it('does not convert non-200 responses', function () {
    $request = Request::create('/not-found');
    $request->headers->set('Accept', 'text/markdown');

    $response = handleMiddleware($request, '<h1>Not Found</h1>', 404);

    expect($response->getStatusCode())->toBe(404);
    expect($response->headers->get('Content-Type'))->not->toBe('text/markdown; charset=UTF-8');
});

it('does not convert non-html responses', function () {
    $request = Request::create('/api/data');
    $request->headers->set('Accept', 'text/markdown');

    $response = handleMiddleware($request, '{"data": "test"}', 200, 'application/json');

    expect($response->headers->get('Content-Type'))->toBe('application/json');
});

it('respects the enabled config', function () {
    config()->set('markdown-response.enabled', false);

    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');

    $response = handleMiddleware($request);

    expect($response->headers->get('Content-Type'))->toBe('text/html');
});

it('respects the doNotProvide attribute', function () {
    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');

    $doNotProvide = new DoNotProvideMarkdownResponse;
    $doNotProvide->handle($request, function ($request) {
        return handleMiddleware($request);
    });

    expect($request->attributes->get('markdown-response.doNotProvide'))->toBeTrue();
});

it('fires events during conversion', function () {
    Event::fake();

    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');

    handleMiddleware($request);

    Event::assertDispatched(ConvertingToMarkdownEvent::class);
    Event::assertDispatched(ConvertedToMarkdownEvent::class);
});

it('does not fire events for normal requests', function () {
    Event::fake();

    $request = Request::create('/about');
    $request->headers->set('User-Agent', 'Mozilla/5.0');

    handleMiddleware($request);

    Event::assertNotDispatched(ConvertingToMarkdownEvent::class);
});
