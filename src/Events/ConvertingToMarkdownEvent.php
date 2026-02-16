<?php

namespace Spatie\MarkdownResponse\Events;

use Illuminate\Http\Request;

class ConvertingToMarkdownEvent
{
    public function __construct(
        public Request $request,
        public string $html,
    ) {}
}
