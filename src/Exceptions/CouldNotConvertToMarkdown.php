<?php

namespace Spatie\MarkdownResponse\Exceptions;

use Exception;

class CouldNotConvertToMarkdown extends Exception
{
    public static function apiError(string $driver, string $body): self
    {
        return new self("The `{$driver}` driver returned an error: {$body}");
    }

    public static function missingCredentials(string $driver): self
    {
        return new self("The `{$driver}` driver requires credentials. Please set them in the config.");
    }
}
