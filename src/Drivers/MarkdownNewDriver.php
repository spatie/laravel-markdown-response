<?php

namespace Spatie\MarkdownResponse\Drivers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Spatie\MarkdownResponse\Exceptions\CouldNotConvertToMarkdown;

class MarkdownNewDriver implements MarkdownDriver
{
    public function __construct(
        protected string $method = 'auto',
        protected bool $retainImages = false,
    ) {}

    public function convert(string $html): string
    {
        $url = request()->fullUrl();

        try {
            $response = Http::post('https://markdown.new/', [
                'url' => $url,
                'method' => $this->method,
                'retain_images' => $this->retainImages,
            ]);
        } catch (ConnectionException $exception) {
            throw CouldNotConvertToMarkdown::apiError('markdown-new', $exception->getMessage());
        }

        if ($response->status() === 429) {
            throw CouldNotConvertToMarkdown::rateLimited('markdown-new');
        }

        if (! $response->successful()) {
            throw CouldNotConvertToMarkdown::apiError('markdown-new', $response->body());
        }

        return $response->body();
    }
}
