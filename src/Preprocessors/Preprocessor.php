<?php

namespace Spatie\MarkdownResponse\Preprocessors;

interface Preprocessor
{
    public function __invoke(string $html): string;
}
