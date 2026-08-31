<?php

namespace Spatie\MarkdownResponse\Postprocessors;

class RemoveHtmlTagsPostprocessor implements Postprocessor
{
    public function __invoke(string $markdown): string
    {
        $markdown = $this->unwrapAutolinks($markdown);

        return strip_tags($markdown);
    }

    /**
     * The league driver renders mailto links and self-referencing urls as
     * markdown autolinks ("<user@example.com>", "<https://example.com>").
     * strip_tags() would treat those as html tags and delete them, so we
     * unwrap them to bare text first.
     */
    protected function unwrapAutolinks(string $markdown): string
    {
        return preg_replace('/<((?:[^\s<>@]+@|https?:\/\/)[^\s<>]+)>/', '$1', $markdown) ?? $markdown;
    }
}
