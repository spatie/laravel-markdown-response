<?php

namespace Spatie\MarkdownResponse;

use Spatie\MarkdownResponse\Drivers\MarkdownDriver;
use Spatie\MarkdownResponse\Postprocessors\Postprocessor;
use Spatie\MarkdownResponse\Preprocessors\Preprocessor;

class HtmlToMarkdownConverter
{
    public function __construct(
        protected MarkdownDriver $driver,
    ) {}

    public function convert(string $html): string
    {
        $html = $this->runPreprocessors($html);

        $markdown = $this->driver->convert($html);

        return $this->runPostprocessors($markdown);
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

    protected function runPostprocessors(string $markdown): string
    {
        $postprocessors = config('markdown-response.postprocessors', []);

        foreach ($postprocessors as $postprocessorClass) {
            /** @var Postprocessor $postprocessor */
            $postprocessor = app($postprocessorClass);

            $markdown = $postprocessor($markdown);
        }

        return $markdown;
    }
}
