<?php

namespace Spatie\MarkdownResponse\Postprocessors;

class RemoveHtmlTagsPostprocessor implements Postprocessor
{
    public function __invoke(string $markdown): string
    {
        // Unwrap CommonMark autolinks before strip_tags() mistakes them for HTML.
        $markdown = preg_replace([
            '/<([a-z][a-z0-9+.-]{1,31}:[^\x00-\x20\x7f<>]*)>/i',
            '/<([a-z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)*)>/i',
        ], '$1', $markdown) ?? $markdown;

        return strip_tags($markdown);
    }
}
