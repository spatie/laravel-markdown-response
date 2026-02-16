<?php

use Spatie\MarkdownResponse\Drivers\LeagueDriver;

it('converts html to markdown', function () {
    $driver = new LeagueDriver;

    $markdown = $driver->convert('<h1>Hello World</h1><p>This is a test.</p>');

    expect($markdown)->toContain('Hello World')
        ->toContain('This is a test.');
});

it('converts links to markdown', function () {
    $driver = new LeagueDriver;

    $markdown = $driver->convert('<a href="https://example.com">Example</a>');

    expect($markdown)->toContain('[Example](https://example.com)');
});

it('respects configured options', function () {
    $driver = new LeagueDriver(['strip_tags' => true]);

    $markdown = $driver->convert('<h1>Hello</h1>');

    expect($markdown)->toBeString();
});
