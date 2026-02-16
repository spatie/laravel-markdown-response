<?php

namespace Spatie\MarkdownResponse\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    public static function actionKeyNotFound(string $actionKey): self
    {
        return new self("The action key `{$actionKey}` was not found in the markdown-response config.");
    }

    public static function actionClassDoesNotExist(string $actionClass): self
    {
        return new self("The configured action class `{$actionClass}` does not exist.");
    }

    public static function actionClassDoesNotExtend(string $actionClass, string $mustExtend): self
    {
        return new self("The configured action class `{$actionClass}` must extend `{$mustExtend}`.");
    }
}
