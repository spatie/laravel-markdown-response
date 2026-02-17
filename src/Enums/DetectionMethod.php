<?php

namespace Spatie\MarkdownResponse\Enums;

enum DetectionMethod: string
{
    case Suffix = 'suffix';
    case Accept = 'accept';
    case UserAgent = 'user-agent';
    case Attribute = 'attribute';
}
