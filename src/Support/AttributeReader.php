<?php

namespace Spatie\MarkdownResponse\Support;

use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

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

        try {
            $reflectionClass = new ReflectionClass($controller);
            $reflectionMethod = $reflectionClass->getMethod($method);

            // Method-level attributes take precedence
            $attribute = self::findAttribute($reflectionMethod, $attributeClasses);

            if ($attribute) {
                return $attribute;
            }

            return self::findAttribute($reflectionClass, $attributeClasses);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * @param  array<class-string>  $attributeClasses
     */
    protected static function findAttribute(
        ReflectionClass|ReflectionMethod $reflection,
        array $attributeClasses,
    ): ?object {
        foreach ($attributeClasses as $attributeClass) {
            $attributes = $reflection->getAttributes($attributeClass);

            if (! empty($attributes)) {
                return $attributes[0]->newInstance();
            }
        }

        return null;
    }
}
