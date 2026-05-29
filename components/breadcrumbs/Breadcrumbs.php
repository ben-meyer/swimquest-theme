<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Breadcrumbs Component
 *
 * Usage:
 *   use Gust\Components\Breadcrumbs;
 *
 *   echo Breadcrumbs::make();
 */
class Breadcrumbs extends ComponentBase
{
    protected static string $name = 'breadcrumbs';

    /**
     * Create a new Breadcrumbs component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return function_exists('yoast_breadcrumb');
    }
}
