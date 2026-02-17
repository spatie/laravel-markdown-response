---
title: Listen to conversion events
weight: 4
---

If you need to monitor or react to conversions, the package fires events at key moments in the process.

## ConvertingToMarkdownEvent

`Spatie\MarkdownResponse\Events\ConvertingToMarkdownEvent`

Fired just before the HTML is converted to markdown. The event receives the `Request` and the HTML string.

## ConvertedToMarkdownEvent

`Spatie\MarkdownResponse\Events\ConvertedToMarkdownEvent`

Fired after the HTML has been converted to markdown. The event receives the `Request` and the resulting markdown string.

## MarkdownCacheHitEvent

`Spatie\MarkdownResponse\Events\MarkdownCacheHitEvent`

Fired when a cached markdown response is returned instead of running a fresh conversion. The event receives the `Request` and the cache key.
