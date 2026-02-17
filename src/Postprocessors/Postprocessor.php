<?php

namespace Spatie\MarkdownResponse\Postprocessors;

interface Postprocessor
{
    public function __invoke(string $markdown): string;
}
