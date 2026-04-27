---
title: CDN and cache layers
weight: 6
---

When you serve both HTML and markdown from the same URL, external cache layers can cause problems. The package includes a `Vary: Accept` header on markdown responses, but not all CDNs respect it.

## Cloudflare

Cloudflare's Free and Pro plans do not respect the `Vary: Accept` header for non-image content types. This means the first response (HTML or markdown) gets cached and served to all subsequent requests — regardless of their Accept header.

If you use the `.md` suffix detection method, this is not an issue: `/about` and `/about.md` are different URLs with separate cache entries.

If you rely on Accept header or user agent detection, you need to configure Cloudflare to bypass its cache for markdown requests. Create a Cache Rule with this expression:

```
any(http.request.headers["accept"][*] contains "text/markdown")
```

Set the action to **Bypass Cache**.

For user agent-based detection, add expressions for the bots you want to handle:

```
any(http.request.headers["accept"][*] contains "text/markdown") or
http.request.headers["user-agent"] contains "GPTBot" or
http.request.headers["user-agent"] contains "ClaudeBot" or
http.request.headers["user-agent"] contains "ChatGPT-User"
```

## Other CDNs

CDNs like Fastly, Varnish, and Nginx generally respect the `Vary: Accept` header. Since the package sets this header on markdown responses, these CDNs will store separate cache entries for different Accept header values without additional configuration.

## spatie/laravel-responsecache

If you use [spatie/laravel-responsecache](https://github.com/spatie/laravel-responsecache), place `ProvideMarkdownResponse` **before** `CacheResponse` in your middleware stack:

```php
$middleware->group('web', [
    // ...
    ProvideMarkdownResponse::class,
    CacheResponse::class,
    // ...
]);
```

With this ordering, `CacheResponse` always caches the original HTML response. Markdown conversion happens after the cache layer on the response path, and `ProvideMarkdownResponse` manages its own markdown cache independently.

If you also want separate cache entries for markdown requests, override `useCacheNameSuffix()` in your `CacheProfile`:

```php
use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;

class CacheProfile extends CacheAllSuccessfulGetRequests
{
    public function useCacheNameSuffix(Request $request): string
    {
        $suffix = parent::useCacheNameSuffix($request);

        if ($request->attributes->get('markdown-response.suffix')
            || str_contains($request->header('Accept', ''), 'text/markdown')
            || $this->hasAiUserAgent($request)
        ) {
            return $suffix . '-markdown';
        }

        return $suffix;
    }

    protected function hasAiUserAgent(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        foreach (config('markdown-response.detection.detect_via_user_agents', []) as $pattern) {
            if (str_contains($userAgent, strtolower($pattern))) {
                return true;
            }
        }

        return false;
    }
}
```

Then register it in `config/responsecache.php`:

```php
'cache_profile' => App\CacheProfiles\CacheProfile::class,
```

## General advice

Caching failures are silent — a cached HTML response served to an AI bot is still a valid HTTP response, so no errors are logged. Test your setup end-to-end in production-like environments where all cache layers are active.
