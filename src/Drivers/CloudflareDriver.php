<?php

namespace Spatie\MarkdownResponse\Drivers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Spatie\MarkdownResponse\Exceptions\CouldNotConvertToMarkdown;

class CloudflareDriver implements MarkdownDriver
{
    public function __construct(
        protected string $accountId,
        protected string $apiToken,
    ) {}

    public function convert(string $html): string
    {
        if (empty($this->accountId) || empty($this->apiToken)) {
            throw CouldNotConvertToMarkdown::missingCredentials('cloudflare');
        }

        try {
            $response = Http::withToken($this->apiToken)
                ->attach('file', $html, 'content.html')
                ->post("https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/ai/tomarkdown");
        } catch (ConnectionException $exception) {
            throw CouldNotConvertToMarkdown::apiError('cloudflare', $exception->getMessage());
        }

        if (! $response->successful()) {
            throw CouldNotConvertToMarkdown::apiError('cloudflare', $response->body());
        }

        return $response->json('result.data', '');
    }
}
