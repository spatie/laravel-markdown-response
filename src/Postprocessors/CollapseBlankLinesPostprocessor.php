<?php

namespace Spatie\MarkdownResponse\Postprocessors;

class CollapseBlankLinesPostprocessor implements Postprocessor
{
    public function __invoke(string $markdown): string
    {
        $markdown = preg_replace('/[ \t]+$/m', '', $markdown);

        $markdown = preg_replace("/\n{3,}/", "\n\n", $markdown);

        return trim($markdown)."\n";
    }
}
