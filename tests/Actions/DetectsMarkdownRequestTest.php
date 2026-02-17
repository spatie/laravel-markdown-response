<?php

use Illuminate\Http\Request;
use Spatie\MarkdownResponse\Actions\DetectsMarkdownRequest;
use Spatie\MarkdownResponse\Enums\DetectionMethod;

beforeEach(function () {
    config()->set('markdown-response.detection.detect_via_accept_header', true);
    config()->set('markdown-response.detection.detect_via_md_suffix', true);
    config()->set('markdown-response.detection.detect_via_user_agents', [
        'GPTBot', 'ClaudeBot', 'ChatGPT-User',
    ]);
});

it('detects md suffix via request attribute', function () {
    $request = Request::create('/about');
    $request->attributes->set('markdown-response.suffix', true);
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe(DetectionMethod::Suffix);
});

it('detects accept header', function () {
    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe(DetectionMethod::Accept);
});

it('detects ai user agent', function () {
    $request = Request::create('/about');
    $request->headers->set('User-Agent', 'Mozilla/5.0 (compatible; GPTBot/1.0)');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe(DetectionMethod::UserAgent);
});

it('returns null for normal requests', function () {
    $request = Request::create('/about');
    $request->headers->set('User-Agent', 'Mozilla/5.0');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBeNull();
});

it('prioritizes suffix over accept header', function () {
    $request = Request::create('/about');
    $request->attributes->set('markdown-response.suffix', true);
    $request->headers->set('Accept', 'text/markdown');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe(DetectionMethod::Suffix);
});

it('can disable accept header detection', function () {
    config()->set('markdown-response.detection.detect_via_accept_header', false);

    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBeNull();
});

it('detects various ai user agents', function (string $userAgent) {
    $request = Request::create('/about');
    $request->headers->set('User-Agent', $userAgent);
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe(DetectionMethod::UserAgent);
})->with([
    'GPTBot/1.0',
    'ClaudeBot/1.0',
    'ChatGPT-User',
]);
