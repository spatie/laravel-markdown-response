<div align="left">
    <a href="https://spatie.be/open-source?utm_source=github&utm_medium=banner&utm_campaign=laravel-markdown-response">
      <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://spatie.be/packages/header/laravel-markdown-response/html/dark.webp">
        <img alt="Logo for laravel-markdown-response" src="https://spatie.be/packages/header/laravel-markdown-response/html/light.webp">
      </picture>
    </a>

<h1>Serve markdown versions of your HTML pages to AI agents and bots</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-markdown-response.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-markdown-response)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-markdown-response/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/laravel-markdown-response/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-markdown-response/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/spatie/laravel-markdown-response/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-markdown-response.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-markdown-response)

</div>

AI agents increasingly consume web content. This package lets your Laravel app serve markdown versions of HTML pages. Markdown requests are detected via `Accept: text/markdown` header, known AI user agent patterns, or `.md` URL suffix. The conversion is driver-based (local PHP or Cloudflare Workers AI), results are cached, and HTML can be preprocessed before conversion.

Here's a quick example:

```php
use Spatie\MarkdownResponse\Middleware\ProvideMarkdownResponse;

Route::middleware(ProvideMarkdownResponse::class)->group(function () {
    Route::get('/about', [PageController::class, 'show']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
});
```

Now when an AI agent visits `/about` or a user visits `/about.md`, they receive a clean markdown version of the page.

You can also convert HTML to markdown directly:

```php
use Spatie\MarkdownResponse\Facades\Markdown;

$markdown = Markdown::convert($html);
```

And test your conversions:

```php
use Spatie\MarkdownResponse\Facades\Markdown;

it('converts the about page to markdown', function () {
    Markdown::fake();

    $this->get('/about.md')->assertOk();

    Markdown::assertConverted();
});
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-markdown-response.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-markdown-response)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/laravel-markdown-response).

## Testing

You can run the tests with:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
