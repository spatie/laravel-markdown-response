<?php

namespace Spatie\MarkdownResponse\Facades;

use Illuminate\Support\Facades\Facade;
use Spatie\MarkdownResponse\Drivers\LeagueDriver;
use Spatie\MarkdownResponse\Fakes\FakeConverter;
use Spatie\MarkdownResponse\HtmlToMarkdownConverter;

/**
 * @method static string convert(string $html)
 * @method static HtmlToMarkdownConverter using(string $driverName)
 *
 * @see HtmlToMarkdownConverter
 */
class Markdown extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HtmlToMarkdownConverter::class;
    }

    public static function fake(): FakeConverter
    {
        $fake = new FakeConverter(new LeagueDriver);

        static::swap($fake);

        return $fake;
    }
}
