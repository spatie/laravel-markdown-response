<?php

use Spatie\MarkdownResponse\Postprocessors\CollapseBlankLinesPostprocessor;
use Spatie\MarkdownResponse\Postprocessors\RemoveHtmlTagsPostprocessor;

it('collapses multiple blank lines into one', function () {
    $postprocessor = new CollapseBlankLinesPostprocessor;

    $markdown = "Hello\n\n\n\n\nWorld\n\n\n\nFoo";
    $result = $postprocessor($markdown);

    expect($result)->toBe("Hello\n\nWorld\n\nFoo\n");
});

it('trims leading and trailing whitespace', function () {
    $postprocessor = new CollapseBlankLinesPostprocessor;

    $markdown = "\n\n\nHello\n\n\n";
    $result = $postprocessor($markdown);

    expect($result)->toBe("Hello\n");
});

it('preserves single blank lines', function () {
    $postprocessor = new CollapseBlankLinesPostprocessor;

    $markdown = "Hello\n\nWorld\n";
    $result = $postprocessor($markdown);

    expect($result)->toBe("Hello\n\nWorld\n");
});

it('collapses lines with only whitespace', function () {
    $postprocessor = new CollapseBlankLinesPostprocessor;

    $markdown = "Hello\n   \n   \n   \nWorld\n";
    $result = $postprocessor($markdown);

    expect($result)->toBe("Hello\n\nWorld\n");
});

it('preserves trailing two spaces used for markdown hard breaks', function () {
    $postprocessor = new CollapseBlankLinesPostprocessor;

    $markdown = "I have a break  \nin my line\n";
    $result = $postprocessor($markdown);

    expect($result)->toBe("I have a break  \nin my line\n");
});

it('strips remaining html tags from markdown', function () {
    $postprocessor = new RemoveHtmlTagsPostprocessor;

    $markdown = '<span class="hl-keyword">use</span> Foo;';
    $result = $postprocessor($markdown);

    expect($result)->toBe('use Foo;');
});

it('does not affect markdown syntax', function () {
    $postprocessor = new RemoveHtmlTagsPostprocessor;

    $markdown = "# Hello\n\n[link](https://example.com)\n\n`code`";
    $result = $postprocessor($markdown);

    expect($result)->toBe("# Hello\n\n[link](https://example.com)\n\n`code`");
});

it('preserves autolink text while stripping html tags', function (string $autolink, string $text) {
    $postprocessor = new RemoveHtmlTagsPostprocessor;

    $markdown = "See <{$autolink}> or <span>read on</span>.";
    $result = $postprocessor($markdown);

    expect($result)->toBe("See {$text} or read on.");
})->with([
    'email' => ['privacy@example.com', 'privacy@example.com'],
    'email punctuation' => ["o'connor+privacy@Sub.example-domain.com", "o'connor+privacy@Sub.example-domain.com"],
    'http' => ['http://example.com', 'http://example.com'],
    'https with query and fragment' => ['https://example.com/docs?a=1&b=2#intro', 'https://example.com/docs?a=1&b=2#intro'],
    'uppercase scheme' => ['HTTPS://example.com/docs', 'HTTPS://example.com/docs'],
    'ftp' => ['ftp://example.com/file', 'ftp://example.com/file'],
    'mailto' => ['mailto:privacy@example.com', 'mailto:privacy@example.com'],
    'telephone' => ['tel:+3212345678', 'tel:+3212345678'],
]);

it('does not expose html containing email addresses or urls', function (string $html) {
    $postprocessor = new RemoveHtmlTagsPostprocessor;

    expect($postprocessor("Before{$html}After"))->toBe('BeforeAfter');
})->with([
    'email comment' => '<!--privacy@example.com-->',
    'url comment' => '<!--https://example.com-->',
    'email attribute' => '<span title="privacy@example.com"></span>',
    'url attribute' => '<a href="https://example.com"></a>',
    'autolink in comment' => '<!-- <privacy@example.com> <https://example.com> -->',
]);

it('preserves multiple autolinks without consuming surrounding markdown', function () {
    $postprocessor = new RemoveHtmlTagsPostprocessor;

    expect($postprocessor('**Contact** <privacy@example.com>, <https://example.com> and <ftp://example.com/file>.'))
        ->toBe('**Contact** privacy@example.com, https://example.com and ftp://example.com/file.');
});
