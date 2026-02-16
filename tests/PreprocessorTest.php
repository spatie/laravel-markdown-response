<?php

use Spatie\MarkdownResponse\Preprocessors\RemoveFooter;
use Spatie\MarkdownResponse\Preprocessors\RemoveNavigation;
use Spatie\MarkdownResponse\Preprocessors\RemoveScriptsAndStyles;

it('removes script tags', function () {
    $preprocessor = new RemoveScriptsAndStyles;

    $html = '<p>Hello</p><script>alert("xss")</script><p>World</p>';
    $result = $preprocessor($html);

    expect($result)->toContain('<p>Hello</p>')
        ->toContain('<p>World</p>')
        ->not->toContain('<script>');
});

it('removes style tags', function () {
    $preprocessor = new RemoveScriptsAndStyles;

    $html = '<p>Hello</p><style>body { color: red; }</style>';
    $result = $preprocessor($html);

    expect($result)->toContain('<p>Hello</p>')
        ->not->toContain('<style>');
});

it('removes stylesheet link tags', function () {
    $preprocessor = new RemoveScriptsAndStyles;

    $html = '<link rel="stylesheet" href="/app.css"><p>Hello</p>';
    $result = $preprocessor($html);

    expect($result)->toContain('<p>Hello</p>')
        ->not->toContain('<link');
});

it('does not remove non-stylesheet link tags', function () {
    $preprocessor = new RemoveScriptsAndStyles;

    $html = '<link rel="icon" href="/favicon.ico"><p>Hello</p>';
    $result = $preprocessor($html);

    expect($result)->toContain('<link rel="icon"');
});

it('removes nav elements', function () {
    $preprocessor = new RemoveNavigation;

    $html = '<nav><a href="/">Home</a></nav><main>Content</main>';
    $result = $preprocessor($html);

    expect($result)->toContain('<main>Content</main>')
        ->not->toContain('<nav>');
});

it('removes footer elements', function () {
    $preprocessor = new RemoveFooter;

    $html = '<main>Content</main><footer>Footer stuff</footer>';
    $result = $preprocessor($html);

    expect($result)->toContain('<main>Content</main>')
        ->not->toContain('<footer>');
});
