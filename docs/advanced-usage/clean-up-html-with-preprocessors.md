---
title: Clean up HTML with preprocessors
weight: 1
---

Before HTML is converted to markdown, it runs through preprocessors that strip elements which don't belong in a markdown document, like scripts, navigation menus, or advertising.

The package ships with three preprocessors:

- `RemoveScriptsAndStyles`: strips `<script>` tags, `<style>` tags, and stylesheet `<link>` tags. Enabled by default.
- `RemoveNavigation`: strips `<nav>` elements. Not enabled by default.
- `RemoveFooter`: strips `<footer>` elements. Not enabled by default.

You can configure which preprocessors run in the config file:

```php
// config/markdown-response.php

'preprocessors' => [
    Spatie\MarkdownResponse\Preprocessors\RemoveScriptsAndStyles::class,
    Spatie\MarkdownResponse\Preprocessors\RemoveNavigation::class,
],
```

Preprocessors run in the order they are listed.

## Create a custom preprocessor

A preprocessor is an invokable class that implements the `Preprocessor` interface:

```php
namespace App\Preprocessors;

use Spatie\MarkdownResponse\Preprocessors\Preprocessor;

class RemoveAds implements Preprocessor
{
    public function __invoke(string $html): string
    {
        return preg_replace('/<div\b[^>]*class="[^"]*\bad\b[^"]*"[^>]*>.*?<\/div>/is', '', $html);
    }
}
```

Then register it in the config file:

```php
// config/markdown-response.php

'preprocessors' => [
    Spatie\MarkdownResponse\Preprocessors\RemoveScriptsAndStyles::class,
    App\Preprocessors\RemoveAds::class,
],
```
