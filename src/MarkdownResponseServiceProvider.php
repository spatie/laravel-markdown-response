<?php

namespace Spatie\MarkdownResponse;

use Illuminate\Contracts\Http\Kernel;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\MarkdownResponse\Commands\ClearMarkdownCacheCommand;
use Spatie\MarkdownResponse\Drivers\CloudflareDriver;
use Spatie\MarkdownResponse\Drivers\LeagueDriver;
use Spatie\MarkdownResponse\Drivers\MarkdownDriver;
use Spatie\MarkdownResponse\Drivers\MarkdownNewDriver;
use Spatie\MarkdownResponse\Exceptions\InvalidDriver;

class MarkdownResponseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('markdown-response')
            ->hasConfigFile()
            ->hasCommand(ClearMarkdownCacheCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->registerDrivers();
        $this->registerConverter();

        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(Middleware\RewriteMarkdownUrls::class);
    }

    protected function registerDrivers(): void
    {
        $this->app->singleton('markdown-response.driver.league', function () {
            $options = config('markdown-response.driver_options.league.options', []);

            return new LeagueDriver($options);
        });

        $this->app->singleton('markdown-response.driver.cloudflare', function () {
            return new CloudflareDriver(
                accountId: config('markdown-response.driver_options.cloudflare.account_id', ''),
                apiToken: config('markdown-response.driver_options.cloudflare.api_token', ''),
            );
        });

        $this->app->singleton('markdown-response.driver.markdown-new', function () {
            return new MarkdownNewDriver(
                method: config('markdown-response.driver_options.markdown-new.method', 'auto'),
                retainImages: config('markdown-response.driver_options.markdown-new.retain_images', false),
            );
        });

        $this->app->singleton(MarkdownDriver::class, function () {
            $driver = config('markdown-response.driver', 'league');

            return match ($driver) {
                'league' => $this->app->make('markdown-response.driver.league'),
                'cloudflare' => $this->app->make('markdown-response.driver.cloudflare'),
                'markdown-new' => $this->app->make('markdown-response.driver.markdown-new'),
                default => throw InvalidDriver::unknown($driver),
            };
        });
    }

    protected function registerConverter(): void
    {
        $this->app->singleton(HtmlToMarkdownConverter::class, function () {
            return new HtmlToMarkdownConverter(
                $this->app->make(MarkdownDriver::class),
            );
        });
    }
}
