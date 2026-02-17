---
title: Test your setup
weight: 4
---

To verify your markdown conversion works correctly in tests, you can fake the `Markdown` facade. This prevents actual HTML-to-markdown conversion and lets you make assertions about what was converted.

```php
use Spatie\MarkdownResponse\Facades\Markdown;

beforeEach(function () {
    Markdown::fake();
});
```

## Assert conversions happened

Use `assertConverted` to verify that at least one conversion took place:

```php
Markdown::fake();

$this->get('/about.md')->assertOk();

Markdown::assertConverted();
```

You can pass a callable for more specific assertions. The callable receives the HTML string:

```php
Markdown::assertConverted(fn (string $html) => str_contains($html, '<h1>About</h1>'));
```

## Assert no conversions happened

Use `assertNotConverted` to verify that no conversions were made:

```php
Markdown::fake();

$this->get('/about')->assertOk();

Markdown::assertNotConverted();
```

## Assert conversion count

Use `assertConvertedCount` to verify the exact number of conversions:

```php
Markdown::fake();

$this->get('/about.md');
$this->get('/posts/1.md');
$this->get('/posts/2.md');

Markdown::assertConvertedCount(3);
```
