<?php

namespace Spatie\MarkdownResponse\Preprocessors;

class RemoveFooterPreprocessor implements Preprocessor
{
    public function __invoke(string $html): string
    {
        return preg_replace('/<footer\b[^>]*>.*?<\/footer>/is', '', $html);
    }
}
