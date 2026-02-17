<?php

namespace Spatie\MarkdownResponse;

use Spatie\MarkdownResponse\Drivers\MarkdownDriver;
use Spatie\MarkdownResponse\Preprocessors\Preprocessor;

class HtmlToMarkdownConverter
{
    public function __construct(
        protected MarkdownDriver $driver,
    ) {}

    public function convert(string $html): string
    {
        $html = $this->runPreprocessors($html);

        return $this->driver->convert($html);
    }

    public function using(string $driverName): static
    {
        $this->driver = app("markdown-response.driver.{$driverName}");

        return $this;
    }

    protected function runPreprocessors(string $html): string
    {
        $preprocessors = config('markdown-response.preprocessors', []);

        foreach ($preprocessors as $preprocessorClass) {
            /** @var Preprocessor $preprocessor */
            $preprocessor = app($preprocessorClass);

            $html = $preprocessor($html);
        }

        return $html;
    }
}
