<?php

namespace Spatie\MarkdownResponse\Support;

use Illuminate\Http\Request;
use Spatie\Attributes\Attributes;

class AttributeReader
{
    /**
     * @param  array<class-string>  $attributeClasses
     */
    public static function getFirstAttribute(Request $request, array $attributeClasses): ?object
    {
        if (! $request->route()) {
            return null;
        }

        $action = $request->route()->getAction('controller');

        if (! $action || ! str_contains($action, '@')) {
            return null;
        }

        [$controller, $method] = explode('@', $action);

        if (! class_exists($controller)) {
            return null;
        }

        // Method-level attributes take precedence
        foreach ($attributeClasses as $attributeClass) {
            $attribute = Attributes::onMethod($controller, $method, $attributeClass);

            if ($attribute) {
                return $attribute;
            }
        }

        foreach ($attributeClasses as $attributeClass) {
            $attribute = Attributes::get($controller, $attributeClass);

            if ($attribute) {
                return $attribute;
            }
        }

        return null;
    }
}
