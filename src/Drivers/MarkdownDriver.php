<?php

namespace Spatie\MarkdownResponse\Drivers;

interface MarkdownDriver
{
    public function convert(string $html): string;
}
