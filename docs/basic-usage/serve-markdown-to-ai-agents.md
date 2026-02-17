---
title: Serve markdown to AI agents
weight: 1
---

AI agents increasingly consume web content, but HTML is noisy: full of navigation, scripts, and styling that gets in the way. By adding the `ProvideMarkdownResponse` middleware to your routes, you can serve clean markdown versions of your pages without changing your existing controllers or views.

```php
use Spatie\MarkdownResponse\Middleware\ProvideMarkdownResponse;

Route::middleware(ProvideMarkdownResponse::class)->group(function () {
    Route::get('/about', [PageController::class, 'show']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
});
```

The middleware detects markdown requests through three mechanisms:

1. URL suffix: if the URL ends in `.md`, the suffix is stripped and the request is routed normally. For example, `/about.md` becomes `/about`.
2. Accept header: if the request includes `Accept: text/markdown`, the middleware swaps it to `text/html` so the app responds normally, then converts the output.
3. User agent: if the request comes from a known AI bot (like GPTBot or ClaudeBot), the middleware converts the HTML response to markdown.

Only responses with a `200` status code, a `text/html` Content-Type, and that are regular `Illuminate\Http\Response` instances get converted. JSON responses, redirects, error pages, and streamed responses pass through unchanged. This means Inertia.js XHR responses work without any issues: only the initial full-page HTML load gets converted.

## Apply the middleware globally

Instead of applying it per route group, you can add it to all routes:

```php
// bootstrap/app.php

use Spatie\MarkdownResponse\Middleware\ProvideMarkdownResponse;

->withMiddleware(function (Middleware $middleware) {
    $middleware->append(ProvideMarkdownResponse::class);
})
```

## Exclude specific routes

If you apply the middleware globally but want to exclude certain routes, use the `DoNotProvideMarkdownResponse` middleware:

```php
use Spatie\MarkdownResponse\Middleware\DoNotProvideMarkdownResponse;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(DoNotProvideMarkdownResponse::class);
```

## Disable the middleware

You can turn off markdown conversion entirely via an environment variable:

```env
MARKDOWN_RESPONSE_ENABLED=false
```
