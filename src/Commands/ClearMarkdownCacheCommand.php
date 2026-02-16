<?php

namespace Spatie\MarkdownResponse\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearMarkdownCacheCommand extends Command
{
    protected $signature = 'markdown-response:clear';

    protected $description = 'Clear the markdown response cache';

    public function handle(): int
    {
        $store = Cache::store(config('markdown-response.cache.store'));

        $store->clear();

        $this->comment('Markdown response cache cleared.');

        return self::SUCCESS;
    }
}
