<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Spatie\MarkdownResponse\Attributes\DoNotProvideMarkdown;
use Spatie\MarkdownResponse\Attributes\ProvideMarkdown;
use Spatie\MarkdownResponse\Middleware\ProvideMarkdownResponse;
use Spatie\MarkdownResponse\Middleware\RewriteMarkdownUrls;

function attributeTestResponse(string $uri, array $headers = []): \Illuminate\Testing\TestResponse
{
    return test()->get($uri, $headers);
}

beforeEach(function () {
    config()->set('markdown-response.cache.enabled', false);
});

it('converts response when controller method has ProvideMarkdown attribute', function () {
    Route::middleware([RewriteMarkdownUrls::class, ProvideMarkdownResponse::class])
        ->get('/provide-method', [ProvideMarkdownMethodController::class, 'index']);

    $response = attributeTestResponse('/provide-method');

    expect($response->headers->get('Content-Type'))->toBe('text/markdown; charset=UTF-8');
});

it('does not convert response when controller method has DoNotProvideMarkdown attribute', function () {
    Route::middleware([RewriteMarkdownUrls::class, ProvideMarkdownResponse::class])
        ->get('/do-not-provide-method', [DoNotProvideMarkdownMethodController::class, 'index']);

    $response = attributeTestResponse('/do-not-provide-method', ['Accept' => 'text/markdown']);

    expect($response->headers->get('Content-Type'))->toContain('text/html');
});

it('converts response when controller class has ProvideMarkdown attribute', function () {
    Route::middleware([RewriteMarkdownUrls::class, ProvideMarkdownResponse::class])
        ->get('/provide-class', [ProvideMarkdownClassController::class, 'index']);

    $response = attributeTestResponse('/provide-class');

    expect($response->headers->get('Content-Type'))->toBe('text/markdown; charset=UTF-8');
});

it('does not convert response when controller class has DoNotProvideMarkdown attribute', function () {
    Route::middleware([RewriteMarkdownUrls::class, ProvideMarkdownResponse::class])
        ->get('/do-not-provide-class', [DoNotProvideMarkdownClassController::class, 'index']);

    $response = attributeTestResponse('/do-not-provide-class', ['Accept' => 'text/markdown']);

    expect($response->headers->get('Content-Type'))->toContain('text/html');
});

it('method attribute overrides class attribute', function () {
    Route::middleware([RewriteMarkdownUrls::class, ProvideMarkdownResponse::class])
        ->get('/override', [MethodOverridesClassController::class, 'index']);

    $response = attributeTestResponse('/override', ['Accept' => 'text/markdown']);

    expect($response->headers->get('Content-Type'))->toContain('text/html');
});

it('still detects normally when no attribute is present', function () {
    Route::middleware([RewriteMarkdownUrls::class, ProvideMarkdownResponse::class])
        ->get('/no-attribute', [NoAttributeController::class, 'index']);

    $response = attributeTestResponse('/no-attribute', ['Accept' => 'text/markdown']);

    expect($response->headers->get('Content-Type'))->toBe('text/markdown; charset=UTF-8');
});

it('does not convert for normal request when no attribute is present', function () {
    Route::middleware([RewriteMarkdownUrls::class, ProvideMarkdownResponse::class])
        ->get('/no-attribute-normal', [NoAttributeController::class, 'index']);

    $response = attributeTestResponse('/no-attribute-normal');

    expect($response->headers->get('Content-Type'))->toContain('text/html');
});

// Test controllers

class ProvideMarkdownMethodController
{
    #[ProvideMarkdown]
    public function index()
    {
        return new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);
    }
}

class DoNotProvideMarkdownMethodController
{
    #[DoNotProvideMarkdown]
    public function index()
    {
        return new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);
    }
}

#[ProvideMarkdown]
class ProvideMarkdownClassController
{
    public function index()
    {
        return new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);
    }
}

#[DoNotProvideMarkdown]
class DoNotProvideMarkdownClassController
{
    public function index()
    {
        return new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);
    }
}

#[ProvideMarkdown]
class MethodOverridesClassController
{
    #[DoNotProvideMarkdown]
    public function index()
    {
        return new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);
    }
}

class NoAttributeController
{
    public function index()
    {
        return new Response('<h1>Hello</h1>', 200, ['Content-Type' => 'text/html']);
    }
}
