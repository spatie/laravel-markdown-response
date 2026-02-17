---
title: Choose a conversion driver
weight: 3
---

By default, the package converts HTML to markdown locally using [league/html-to-markdown](https://github.com/thephpleague/html-to-markdown). If you need better conversion quality or JavaScript rendering support, you can switch to an external driver.

Set the driver via the `MARKDOWN_RESPONSE_DRIVER` environment variable.

## Use the League driver (default)

The League driver runs locally, requires no external services, and works out of the box.

```env
MARKDOWN_RESPONSE_DRIVER=league
```

Options are passed directly to the `HtmlConverter` constructor. See the [league/html-to-markdown documentation](https://github.com/thephpleague/html-to-markdown#options) for available options.

```php
// config/markdown-response.php

'driver_options' => [
    'league' => [
        'options' => [
            'strip_tags' => true,
            'hard_break' => true,
        ],
    ],
],
```

## Use the Cloudflare driver

The Cloudflare driver uses the [Workers AI API](https://developers.cloudflare.com/workers-ai/) to convert HTML to markdown server-side. It sends the HTML as a file upload and returns the converted markdown.

```env
MARKDOWN_RESPONSE_DRIVER=cloudflare
CLOUDFLARE_ACCOUNT_ID=your-account-id
CLOUDFLARE_API_TOKEN=your-api-token
```

To get your credentials:

1. Log in to the [Cloudflare dashboard](https://dash.cloudflare.com)
2. Your Account ID is in the dashboard URL
3. Create an API token under Manage account > Account API tokens

## Use the markdown.new driver

The [markdown.new](https://markdown.new) driver sends the current page URL to an external service that fetches and converts the page. It supports JavaScript rendering via the `browser` method, which is useful for SPAs.

```env
MARKDOWN_RESPONSE_DRIVER=markdown-new
```

```php
// config/markdown-response.php

'driver_options' => [
    'markdown-new' => [
        'method' => 'auto', // 'auto', 'fetch', or 'browser'
        'retain_images' => false,
    ],
],
```

This driver resolves the URL from the current request, so it works automatically with the middleware. It has a free tier of 500 requests/day and throws a `CouldNotConvertToMarkdown` exception when rate limited.

## Create a custom driver

You can create your own driver by implementing the `MarkdownDriver` interface:

```php
namespace App\Drivers;

use Spatie\MarkdownResponse\Drivers\MarkdownDriver;

class PandocDriver implements MarkdownDriver
{
    public function convert(string $html): string
    {
        // Your conversion logic here
    }
}
```

Then bind it to the `MarkdownDriver` interface in a service provider:

```php
use App\Drivers\PandocDriver;
use Spatie\MarkdownResponse\Drivers\MarkdownDriver;

$this->app->singleton(MarkdownDriver::class, PandocDriver::class);
```

## Switch drivers at runtime

You can switch drivers on a per-conversion basis using the `using()` method:

```php
use Spatie\MarkdownResponse\Facades\Markdown;

$markdown = Markdown::using('cloudflare')->convert($html);
```
