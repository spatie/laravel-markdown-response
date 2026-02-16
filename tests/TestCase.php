<?php

namespace Spatie\MarkdownResponse\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\MarkdownResponse\MarkdownResponseServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            MarkdownResponseServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('markdown-response.cache.enabled', false);
    }
}
