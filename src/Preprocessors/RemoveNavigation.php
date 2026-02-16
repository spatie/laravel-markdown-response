<?php

namespace Spatie\MarkdownResponse\Preprocessors;

class RemoveNavigation implements Preprocessor
{
    public function __invoke(string $html): string
    {
        return preg_replace('/<nav\b[^>]*>.*?<\/nav>/is', '', $html);
    }
}
