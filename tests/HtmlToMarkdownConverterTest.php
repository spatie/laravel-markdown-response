<?php

use Spatie\MarkdownResponse\Facades\Markdown;
use Spatie\MarkdownResponse\HtmlToMarkdownConverter;
use Spatie\MarkdownResponse\Postprocessors\RemoveHtmlTagsPostprocessor;
use Spatie\MarkdownResponse\Preprocessors\RemoveScriptsAndStylesPreprocessor;

it('converts html to markdown via the converter', function () {
    $converter = app(HtmlToMarkdownConverter::class);

    $markdown = $converter->convert('<h1>Hello</h1><p>World</p>');

    expect($markdown)->toContain('Hello')
        ->toContain('World');
});

it('runs preprocessors before converting', function () {
    config()->set('markdown-response.preprocessors', [
        RemoveScriptsAndStylesPreprocessor::class,
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
        RemoveHtmlTagsPostprocessor::class,
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

it('keeps autolink text through conversion and postprocessing', function (string $html, string $expected) {
    config()->set('markdown-response.postprocessors', [
        RemoveHtmlTagsPostprocessor::class,
    ]);

    $converter = app(HtmlToMarkdownConverter::class);

    expect($converter->convert($html))->toBe($expected);
})->with([
    'email' => [
        '<p>Email <a href="mailto:privacy@example.com">privacy@example.com</a> for help.</p>',
        'Email privacy@example.com for help.',
    ],
    'https' => [
        '<p>Visit <a href="https://example.com/docs">https://example.com/docs</a> for help.</p>',
        'Visit https://example.com/docs for help.',
    ],
    'uppercase https' => [
        '<p>Visit <a href="HTTPS://example.com/docs">HTTPS://example.com/docs</a> for help.</p>',
        'Visit HTTPS://example.com/docs for help.',
    ],
    'ftp' => [
        '<p>Download <a href="ftp://example.com/file">ftp://example.com/file</a> now.</p>',
        'Download ftp://example.com/file now.',
    ],
    'named link' => [
        '<p><a href="mailto:privacy@example.com">Contact us</a> for help.</p>',
        '[Contact us](mailto:privacy@example.com) for help.',
    ],
]);

it('keeps autolinks through the default postprocessor pipeline', function () {
    expect(Markdown::convert('<p>Email <a href="mailto:privacy@example.com">privacy@example.com</a> or visit <a href="https://example.com">https://example.com</a>.</p>'))
        ->toBe("Email privacy@example.com or visit https://example.com.\n");
});
