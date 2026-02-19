---
title: Response headers
weight: 7
---

Every markdown response includes a set of headers that help AI agents understand and process the content.

## Default headers

These headers are always included:

| Header | Value | Purpose |
|---|---|---|
| `Content-Type` | `text/markdown; charset=UTF-8` | Identifies the response as markdown |
| `Vary` | `Accept` | Tells caches to store separate entries per Accept header |
| `X-Robots-Tag` | `noindex` | Prevents search engines from indexing the markdown version |
| `X-Markdown-Tokens` | e.g. `1234` | Estimated token count of the markdown content |

The token count is a rough estimate based on the character length of the markdown (characters / 4). It lets AI agents gauge content size from headers before downloading the body.

## Content-Signal header

The `Content-Signal` header communicates to AI agents what they are allowed to do with your content. By default, the package sends:

```
Content-Signal: ai-train=disallow, ai-input=allow, search=allow
```

You can customize these signals in the config file:

```php
// config/markdown-response.php

'content_signals' => [
    'ai-train' => 'disallow',
    'ai-input' => 'allow',
    'search' => 'allow',
],
```

Set the array to empty to disable the header entirely:

```php
'content_signals' => [],
```
