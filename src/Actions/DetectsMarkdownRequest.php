<?php

namespace Spatie\MarkdownResponse\Actions;

use Illuminate\Http\Request;
use Spatie\MarkdownResponse\Enums\DetectionMethod;

class DetectsMarkdownRequest
{
    public function __invoke(Request $request): ?DetectionMethod
    {
        if ($this->hasMdSuffix($request)) {
            return DetectionMethod::Suffix;
        }

        if ($this->hasAcceptHeader($request)) {
            return DetectionMethod::Accept;
        }

        if ($this->hasAiUserAgent($request)) {
            return DetectionMethod::UserAgent;
        }

        return null;
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
