---
title: Clean up markdown with postprocessors
weight: 2
---

After HTML is converted to markdown, the result runs through postprocessors. These clean up the markdown output, for example by removing leftover HTML tags or collapsing excessive blank lines.

The package ships with two postprocessors:

- `RemoveHtmlTagsPostprocessor`: strips any HTML tags that survived the conversion, such as `<span>` tags from syntax highlighting. Enabled by default.
- `CollapseBlankLinesPostprocessor`: reduces three or more consecutive blank lines to a single blank line, and trims leading/trailing whitespace. Enabled by default.

You can configure which postprocessors run in the config file:

```php
// config/markdown-response.php

'postprocessors' => [
    Spatie\MarkdownResponse\Postprocessors\RemoveHtmlTagsPostprocessor::class,
    Spatie\MarkdownResponse\Postprocessors\CollapseBlankLinesPostprocessor::class,
],
```

Postprocessors run in the order they are listed.

## Create a custom postprocessor

A postprocessor is an invokable class that implements the `Postprocessor` interface:

```php
namespace App\Postprocessors;

use Spatie\MarkdownResponse\Postprocessors\Postprocessor;

class AddFrontMatter implements Postprocessor
{
    public function __invoke(string $markdown): string
    {
        return "---\nsource: my-app\n---\n\n" . $markdown;
    }
}
```

Then register it in the config file:

```php
// config/markdown-response.php

'postprocessors' => [
    Spatie\MarkdownResponse\Postprocessors\RemoveHtmlTagsPostprocessor::class,
    Spatie\MarkdownResponse\Postprocessors\CollapseBlankLinesPostprocessor::class,
    App\Postprocessors\AddFrontMatter::class,
],
```
