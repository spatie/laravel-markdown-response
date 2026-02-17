<?php

use Spatie\MarkdownResponse\Facades\Markdown;
use Spatie\MarkdownResponse\HtmlToMarkdownConverter;

it('converts html to markdown via the converter', function () {
    $converter = app(HtmlToMarkdownConverter::class);

    $markdown = $converter->convert('<h1>Hello</h1><p>World</p>');

    expect($markdown)->toContain('Hello')
        ->toContain('World');
});

it('runs preprocessors before converting', function () {
    config()->set('markdown-response.preprocessors', [
        Spatie\MarkdownResponse\Preprocessors\RemoveScriptsAndStylesPreprocessor::class,
    ]);

    $converter = app(HtmlToMarkdownConverter::class);

    $markdown = $converter->convert('<h1>Hello</h1><script>alert("xss")</script>');

    expect($markdown)->toContain('Hello')
        ->not->toContain('alert');
});

it('works via the facade', function () {
    $markdown = Markdown::convert('<h1>Hello</h1>');

    expect($markdown)->toContain('Hello');
});

it('runs postprocessors after converting', function () {
    config()->set('markdown-response.postprocessors', [
        Spatie\MarkdownResponse\Postprocessors\RemoveHtmlTagsPostprocessor::class,
    ]);

    $converter = app(HtmlToMarkdownConverter::class);

    $markdown = $converter->convert('<pre><span class="hl">code</span></pre>');

    expect($markdown)->toContain('code')
        ->not->toContain('<span');
});

it('can switch drivers via using()', function () {
    $converter = app(HtmlToMarkdownConverter::class);

    expect($converter->using('league'))->toBe($converter);
    expect($converter->convert('<h1>Test</h1>'))->toContain('Test');
});
