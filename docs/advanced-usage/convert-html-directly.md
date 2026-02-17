---
title: Convert HTML directly
weight: 5
---

Besides automatic conversion via the middleware, you can convert HTML to markdown anywhere in your code using the `Markdown` facade:

```php
use Spatie\MarkdownResponse\Facades\Markdown;

$markdown = Markdown::convert('<h1>Hello</h1><p>World</p>');
```

The conversion uses whichever driver is configured in `markdown-response.driver` and runs all configured preprocessors before converting.

You can switch drivers on the fly using the `using()` method:

```php
$markdown = Markdown::using('cloudflare')->convert($html);
```
