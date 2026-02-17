<?php

namespace Spatie\MarkdownResponse\Postprocessors;

class RemoveHtmlTagsPostprocessor implements Postprocessor
{
    public function __invoke(string $markdown): string
    {
        return strip_tags($markdown);
    }
}
