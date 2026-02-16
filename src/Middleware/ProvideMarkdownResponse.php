<?php

namespace Spatie\MarkdownResponse\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Spatie\MarkdownResponse\Actions\DetectsMarkdownRequest;
use Spatie\MarkdownResponse\Actions\GeneratesCacheKey;
use Spatie\MarkdownResponse\Events\ConvertedToMarkdownEvent;
use Spatie\MarkdownResponse\Events\ConvertingToMarkdownEvent;
use Spatie\MarkdownResponse\Events\MarkdownCacheHitEvent;
use Spatie\MarkdownResponse\HtmlToMarkdownConverter;
use Spatie\MarkdownResponse\Support\Config;

class ProvideMarkdownResponse
{
    public function handle(Request $request, Closure $next): mixed
    {
        $detectionMethod = $this->shouldConvertToMarkdown($request);

        if ($detectionMethod === false) {
            return $next($request);
        }

        if ($detectionMethod === 'suffix') {
            $this->rewriteUrlWithoutMdSuffix($request);
        }

        $cacheKey = $this->generateCacheKey($request);

        if ($markdown = $this->getCachedMarkdown($request, $cacheKey)) {
            return $this->markdownResponse($markdown);
        }

        $response = $this->getHtmlResponse($request, $next, $detectionMethod);

        if (! $this->isConvertibleResponse($response)) {
            return $response;
        }

        return $this->convertAndCacheResponse($request, $response, $cacheKey);
    }

    protected function shouldConvertToMarkdown(Request $request): string|false
    {
        if (! config('markdown-response.enabled', true)) {
            return false;
        }

        if ($request->attributes->get('markdown-response.doNotProvide')) {
            return false;
        }

        return Config::getAction('detection.detector', DetectsMarkdownRequest::class)($request);
    }

    protected function generateCacheKey(Request $request): string
    {
        return Config::getAction('cache.key_generator', GeneratesCacheKey::class)($request);
    }

    protected function getCachedMarkdown(Request $request, string $cacheKey): ?string
    {
        if (! config('markdown-response.cache.enabled', true)) {
            return null;
        }

        $cached = Cache::store(config('markdown-response.cache.store'))->get($cacheKey);

        if ($cached !== null) {
            event(new MarkdownCacheHitEvent($request, $cacheKey));
        }

        return $cached;
    }

    protected function getHtmlResponse(Request $request, Closure $next, string $detectionMethod): mixed
    {
        if ($detectionMethod !== 'accept') {
            return $next($request);
        }

        $originalAccept = $request->headers->get('Accept');
        $request->headers->set('Accept', 'text/html');

        $response = $next($request);

        $request->headers->set('Accept', $originalAccept);

        return $response;
    }

    protected function isConvertibleResponse(mixed $response): bool
    {
        if (! $response instanceof Response) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html');
    }

    protected function convertAndCacheResponse(Request $request, Response $response, string $cacheKey): Response
    {
        $html = $response->getContent();

        event(new ConvertingToMarkdownEvent($request, $html));

        $markdown = app(HtmlToMarkdownConverter::class)->convert($html);

        event(new ConvertedToMarkdownEvent($request, $markdown));

        if (config('markdown-response.cache.enabled', true)) {
            $ttl = config('markdown-response.cache.ttl', 3600);
            Cache::store(config('markdown-response.cache.store'))->put($cacheKey, $markdown, $ttl);
        }

        return $this->markdownResponse($markdown);
    }

    protected function rewriteUrlWithoutMdSuffix(Request $request): void
    {
        $path = preg_replace('/\.md$/', '', $request->getPathInfo());
        $queryString = $request->getQueryString();
        $uri = $path.($queryString ? "?{$queryString}" : '');

        $request->server->set('REQUEST_URI', $uri);

        $request->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->getContent(),
        );
    }

    protected function markdownResponse(string $markdown): Response
    {
        return new Response($markdown, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }
}
