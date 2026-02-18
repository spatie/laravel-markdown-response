---
title: Introduction
weight: 1
---

AI agents increasingly consume web content. This package lets your Laravel app serve markdown versions of HTML pages, making your content more accessible to AI crawlers and tools.

Add the middleware to your routes, and you're done:

```php
use Spatie\MarkdownResponse\Middleware\ProvideMarkdownResponse;

Route::middleware(ProvideMarkdownResponse::class)->group(function () {
    Route::get('/about', [PageController::class, 'show']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
});
```

Now when an AI agent visits `/about` (or a user visits `/about.md`), it receives a clean markdown version of the page instead of HTML. Your existing controllers and views stay exactly the same.

The package detects markdown requests through three mechanisms: `Accept: text/markdown` headers, `.md` URL suffixes, and known AI bot user agents like GPTBot and ClaudeBot.

The HTML-to-markdown conversion is driver-based. The default driver uses [league/html-to-markdown](https://github.com/thephpleague/html-to-markdown) and works locally without any external services. You can also use the Cloudflare Workers AI API for better conversion quality.

Converted responses are cached by default, so repeated requests skip the conversion entirely.

## We got badges

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-markdown-response.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-markdown-response)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-markdown-response/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/laravel-markdown-response/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-markdown-response/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/spatie/laravel-markdown-response/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-markdown-response.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-markdown-response)
