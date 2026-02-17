<?php

namespace Spatie\MarkdownResponse\Actions;

use Illuminate\Http\Request;

class GeneratesCacheKey
{
    public function __invoke(Request $request): string
    {
        $path = preg_replace('/\.md$/', '', $request->getPathInfo());

        $queryString = $this->filteredQueryString($request);

        $raw = $request->getHost().$path.$queryString;

        return 'markdown-response:'.hash('xxh128', $raw);
    }

    protected function filteredQueryString(Request $request): string
    {
        $query = $request->query();

        $ignored = config('markdown-response.cache.ignored_query_parameters', []);

        $filtered = array_diff_key($query, array_flip($ignored));

        if (empty($filtered)) {
            return '';
        }

        ksort($filtered);

        return '?'.http_build_query($filtered);
    }
}
