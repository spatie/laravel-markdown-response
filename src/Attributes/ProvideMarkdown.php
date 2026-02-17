<?php

namespace Spatie\MarkdownResponse\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ProvideMarkdown {}
