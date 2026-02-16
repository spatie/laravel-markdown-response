<?php

namespace Spatie\MarkdownResponse\Events;

use Illuminate\Http\Request;

class ConvertedToMarkdownEvent
{
    public function __construct(
        public Request $request,
        public string $markdown,
    ) {}
}
