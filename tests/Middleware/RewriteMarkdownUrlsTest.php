<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\MarkdownResponse\Middleware\RewriteMarkdownUrls;

function handleRewrite(Request $request): Request
{
    $middleware = new RewriteMarkdownUrls;

    $middleware->handle($request, function ($request) {
        return new Response('ok');
    });

    return $request;
}

it('strips .md suffix and sets attribute', function () {
    $request = Request::create('/about.md');

    handleRewrite($request);

    expect($request->getPathInfo())->toBe('/about');
    expect($request->attributes->get('markdown-response.suffix'))->toBeTrue();
});

it('does not modify non-.md requests', function () {
    $request = Request::create('/about');

    handleRewrite($request);

    expect($request->getPathInfo())->toBe('/about');
    expect($request->attributes->get('markdown-response.suffix'))->toBeNull();
});

it('can be disabled via config', function () {
    config()->set('markdown-response.detection.detect_via_md_suffix', false);

    $request = Request::create('/about.md');

    handleRewrite($request);

    expect($request->getPathInfo())->toBe('/about.md');
    expect($request->attributes->get('markdown-response.suffix'))->toBeNull();
});

it('preserves query string when rewriting', function () {
    $request = Request::create('/about.md?page=2');

    handleRewrite($request);

    expect($request->getPathInfo())->toBe('/about');
    expect($request->query('page'))->toBe('2');
});
