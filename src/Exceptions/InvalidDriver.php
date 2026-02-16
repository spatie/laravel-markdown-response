<?php

namespace Spatie\MarkdownResponse\Exceptions;

use Exception;

class InvalidDriver extends Exception
{
    public static function unknown(string $driver): self
    {
        return new self("The driver `{$driver}` is not supported.");
    }
}
