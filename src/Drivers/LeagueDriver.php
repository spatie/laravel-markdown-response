<?php

namespace Spatie\MarkdownResponse\Drivers;

use League\HTMLToMarkdown\HtmlConverter;

class LeagueDriver implements MarkdownDriver
{
    public function __construct(
        protected array $options = [],
    ) {}

    public function convert(string $html): string
    {
        $converter = new HtmlConverter($this->options);

        return $converter->convert($html);
    }
}
