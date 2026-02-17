---
title: Cache converted responses
weight: 2
---

Converting HTML to markdown on every request would be wasteful, so the package caches results by default. When a cached response exists, it's returned directly without running any controllers or conversion logic.

Caching is enabled with a one-hour TTL out of the box. You can adjust this via environment variables:

```env
MARKDOWN_RESPONSE_CACHE_ENABLED=true
MARKDOWN_RESPONSE_CACHE_STORE=redis
MARKDOWN_RESPONSE_CACHE_TTL=3600
```

Set `MARKDOWN_RESPONSE_CACHE_STORE` to any cache store configured in `config/cache.php`. When left empty, the default cache store is used.

## Understand cache keys

The cache key is generated from the request's host, path, and query string. Common tracking parameters like `utm_source`, `gclid`, and `fbclid` are stripped, so `https://example.com/about` and `https://example.com/about?utm_source=google` share the same cached response.

URLs ending in `.md` share the same cache key as their non-suffixed counterpart. So `/about.md` and `/about` (with `Accept: text/markdown`) hit the same cache entry.

You can customize the list of ignored parameters in the config file:

```php
// config/markdown-response.php

'cache' => [
    'ignored_query_parameters' => [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        // ...
    ],
],
```

## Clear the cache

You can clear the markdown cache with an artisan command:

```bash
php artisan markdown-response:clear
```

> Note: this flushes the entire configured cache store. If you're using a shared cache store, consider using a dedicated store for markdown responses.

## Customize cache key generation

If the default cache key strategy doesn't fit your needs, you can extend the `GeneratesCacheKey` class and point to it in the config:

```php
// config/markdown-response.php

'cache' => [
    'key_generator' => App\Actions\CustomCacheKey::class,
],
```

```php
namespace App\Actions;

use Illuminate\Http\Request;
use Spatie\MarkdownResponse\Actions\GeneratesCacheKey;

class CustomCacheKey extends GeneratesCacheKey
{
    public function __invoke(Request $request): string
    {
        return 'markdown-response:' . hash('xxh128', $request->fullUrl());
    }
}
```
