<?php

namespace Spatie\MarkdownResponse\Preprocessors;

class RemoveHeaderPreprocessor implements Preprocessor
{
    public function __invoke(string $html): string
    {
        return preg_replace('/<header\b[^>]*>.*?<\/header>/is', '', $html);
    }
}
