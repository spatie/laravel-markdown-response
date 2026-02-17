---
title: Customize request detection
weight: 3
---

The built-in detector handles most cases, but you can fine-tune which detection methods are active or replace the detector entirely.

## Toggle detection methods

You can enable or disable individual detection methods in the config:

```php
// config/markdown-response.php

'detection' => [
    'detect_via_accept_header' => true,
    'detect_via_md_suffix' => true,
    'detect_via_user_agents' => [
        'GPTBot',
        'ClaudeBot',
        // ...
    ],
],
```

Set `detect_via_user_agents` to an empty array to disable user agent detection.

## Extend the detector

If you need more control, you can extend the `DetectsMarkdownRequest` class and point to it in the config:

```php
// config/markdown-response.php

'detection' => [
    'detector' => App\Actions\CustomDetectsMarkdownRequest::class,
],
```

```php
namespace App\Actions;

use Illuminate\Http\Request;
use Spatie\MarkdownResponse\Actions\DetectsMarkdownRequest;

class CustomDetectsMarkdownRequest extends DetectsMarkdownRequest
{
    public function __invoke(Request $request): false|string
    {
        // Only respond with markdown for specific paths
        if (! str_starts_with($request->getPathInfo(), '/docs')) {
            return false;
        }

        return parent::__invoke($request);
    }
}
```

The returned string tells the middleware how to handle the request:

- `'suffix'`: the middleware strips `.md` from the URL before routing
- `'accept'`: the middleware temporarily swaps the Accept header to `text/html`
- `'user-agent'`: the middleware lets the request pass through normally
