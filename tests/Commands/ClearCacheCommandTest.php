<?php

it('can clear the markdown cache', function () {
    $this->artisan('markdown-response:clear')
        ->expectsOutputToContain('Markdown response cache cleared.')
        ->assertSuccessful();
});
