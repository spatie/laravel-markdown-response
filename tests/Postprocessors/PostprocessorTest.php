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
