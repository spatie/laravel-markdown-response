<?php

use Illuminate\Support\Facades\Http;
use Spatie\MarkdownResponse\Drivers\CloudflareDriver;
use Spatie\MarkdownResponse\Exceptions\CouldNotConvertToMarkdown;

it('returns the converted markdown from the cloudflare response', function () {
    Http::fake([
        'api.cloudflare.com/*' => Http::response([
            'success' => true,
            'result' => [
                [
                    'name' => 'content.html',
                    'mimetype' => 'text/html',
                    'format' => 'markdown',
                    'tokens' => 12,
                    'data' => "Hello world\n\nFrom Cloudflare",
                ],
            ],
        ]),
    ]);

    $driver = new CloudflareDriver('account-id', 'api-token');

    expect($driver->convert('<p>Hello world</p>'))->toBe("Hello world\n\nFrom Cloudflare");
});

it('throws when credentials are missing', function () {
    $driver = new CloudflareDriver('', '');

    $driver->convert('<p>Hello world</p>');
})->throws(CouldNotConvertToMarkdown::class);

it('throws when the cloudflare response is unsuccessful', function () {
    Http::fake([
        'api.cloudflare.com/*' => Http::response(['success' => false], 500),
    ]);

    $driver = new CloudflareDriver('account-id', 'api-token');

    $driver->convert('<p>Hello world</p>');
})->throws(CouldNotConvertToMarkdown::class);
