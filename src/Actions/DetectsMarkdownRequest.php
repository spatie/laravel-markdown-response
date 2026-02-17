<?php

namespace Spatie\MarkdownResponse\Actions;

use Illuminate\Http\Request;

class DetectsMarkdownRequest
{
    public function __invoke(Request $request): false|string
    {
        if ($this->hasMdSuffix($request)) {
            return 'suffix';
        }

        if ($this->hasAcceptHeader($request)) {
            return 'accept';
        }

        if ($this->hasAiUserAgent($request)) {
            return 'user-agent';
        }

        return false;
    }

    protected function hasMdSuffix(Request $request): bool
    {
        return (bool) $request->attributes->get('markdown-response.suffix');
    }

    protected function hasAcceptHeader(Request $request): bool
    {
        if (! config('markdown-response.detection.detect_via_accept_header', true)) {
            return false;
        }

        return str_contains($request->header('Accept', ''), 'text/markdown');
    }

    protected function hasAiUserAgent(Request $request): bool
    {
        $userAgent = $request->userAgent() ?? '';

        $patterns = config('markdown-response.detection.detect_via_user_agents', []);

        foreach ($patterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
