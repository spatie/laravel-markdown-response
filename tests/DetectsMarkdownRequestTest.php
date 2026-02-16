<?php

use Illuminate\Http\Request;
use Spatie\MarkdownResponse\Actions\DetectsMarkdownRequest;

beforeEach(function () {
    config()->set('markdown-response.detection.detect_via_accept_header', true);
    config()->set('markdown-response.detection.detect_via_md_suffix', true);
    config()->set('markdown-response.detection.detect_via_user_agents', [
        'GPTBot', 'ClaudeBot', 'ChatGPT-User',
    ]);
});

it('detects md suffix', function () {
    $request = Request::create('/about.md');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe('suffix');
});

it('detects accept header', function () {
    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe('accept');
});

it('detects ai user agent', function () {
    $request = Request::create('/about');
    $request->headers->set('User-Agent', 'Mozilla/5.0 (compatible; GPTBot/1.0)');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe('user-agent');
});

it('returns false for normal requests', function () {
    $request = Request::create('/about');
    $request->headers->set('User-Agent', 'Mozilla/5.0');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBeFalse();
});

it('prioritizes suffix over accept header', function () {
    $request = Request::create('/about.md');
    $request->headers->set('Accept', 'text/markdown');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe('suffix');
});

it('can disable md suffix detection', function () {
    config()->set('markdown-response.detection.detect_via_md_suffix', false);

    $request = Request::create('/about.md');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBeFalse();
});

it('can disable accept header detection', function () {
    config()->set('markdown-response.detection.detect_via_accept_header', false);

    $request = Request::create('/about');
    $request->headers->set('Accept', 'text/markdown');
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBeFalse();
});

it('detects various ai user agents', function (string $userAgent) {
    $request = Request::create('/about');
    $request->headers->set('User-Agent', $userAgent);
    $detector = new DetectsMarkdownRequest;

    expect($detector($request))->toBe('user-agent');
})->with([
    'GPTBot/1.0',
    'ClaudeBot/1.0',
    'ChatGPT-User',
]);
