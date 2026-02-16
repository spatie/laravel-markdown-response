<?php

namespace Spatie\MarkdownResponse\Middleware;

use Closure;
use Illuminate\Http\Request;

class DoNotProvideMarkdownResponse
{
    public function handle(Request $request, Closure $next): mixed
    {
        $request->attributes->set('markdown-response.doNotProvide', true);

        return $next($request);
    }
}
