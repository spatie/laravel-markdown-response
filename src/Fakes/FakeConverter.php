<?php

namespace Spatie\MarkdownResponse\Fakes;

use Closure;
use PHPUnit\Framework\Assert;
use Spatie\MarkdownResponse\HtmlToMarkdownConverter;

class FakeConverter extends HtmlToMarkdownConverter
{
    /** @var array<int, string> */
    protected array $conversions = [];

    public function convert(string $html): string
    {
        $this->conversions[] = $html;

        return '# Fake Markdown Response';
    }

    public function assertConverted(?Closure $callback = null): void
    {
        if ($callback === null) {
            Assert::assertNotEmpty($this->conversions, 'Expected at least one conversion, but none were made.');

            return;
        }

        $matching = array_filter(
            $this->conversions,
            fn (string $html) => $callback($html),
        );

        Assert::assertNotEmpty($matching, 'Expected a matching conversion, but none were found.');
    }

    public function assertNotConverted(): void
    {
        Assert::assertEmpty($this->conversions, 'Expected no conversions, but some were made.');
    }

    public function assertConvertedCount(int $count): void
    {
        Assert::assertCount($count, $this->conversions);
    }
}
