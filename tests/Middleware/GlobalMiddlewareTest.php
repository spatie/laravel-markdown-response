<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Spatie\MarkdownResponse\MarkdownResponseServiceProvider;
use Spatie\MarkdownResponse\Middleware\ProvideMarkdownResponse;

it('serves markdown for .md urls when the middleware is applied globally', function () {
    // the application's global middleware is set before packages register theirs
    $kernel = app(Kernel::class);
    $kernel->setGlobalMiddleware([ProvideMarkdownResponse::class]);
    (new MarkdownResponseServiceProvider(app()))->packageRegistered();

    Route::get('/about', fn () => '<html><body><h1>About us</h1></body></html>');

    $this->get('/about.md')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
        ->assertSee('About us');
});
