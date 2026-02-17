<?php

namespace Spatie\MarkdownResponse\Middleware;

use Closure;
use Illuminate\Http\Request;

class RewriteMarkdownUrls
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! config('markdown-response.detection.detect_via_md_suffix', true)) {
            return $next($request);
        }

        if (! str_ends_with($request->getPathInfo(), '.md')) {
            return $next($request);
        }

        $this->rewriteUrlWithoutMdSuffix($request);

        $request->attributes->set('markdown-response.suffix', true);

        return $next($request);
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
}
