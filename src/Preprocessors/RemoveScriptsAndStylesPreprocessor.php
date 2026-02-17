<?php

namespace Spatie\MarkdownResponse\Preprocessors;

class RemoveScriptsAndStylesPreprocessor implements Preprocessor
{
    public function __invoke(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<link\b[^>]*rel=["\']stylesheet["\'][^>]*\/?>/is', '', $html);

        return $html;
    }
}
