<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Usage:
 *
 * If no variant is specified, the button will use the primary style by default.
 * 
 *   <?php
 *   echo Button::make(content: 'Book Now', variant: 'primary');
 *   echo Button::make(content: 'Learn More', variant: 'secondary');
 *   echo Button::make(content: 'Unavailable', variant: 'inactive');
 */

/**
 * Button Component
 *
 * Usage:
 *   use Gust\Components\Button;
 *
 *   echo Button::make();
 */
class Button extends ComponentBase
{
    protected static string $name = 'button';

    /**
     * Create a new Button component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        string $content = '',
        array $classes = [],
        bool $screen_reader_text = false,
        string $type = 'button',
        string $variant = 'primary',
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['content']);
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['screen_reader_text'])) {
            $args['content'] = Element::make(
                el: 'span',
                content: $args['content'],
                classes: ['screen-reader-text'],
            );
        }

        $args['attributes']['type'] = $args['type'];

        $variant_classes = [
            'secondary' => 'btn--theme-2',
            'inactive' => 'btn--inactive',
            'ghost' => 'btn--ghost',
        ];
        if (isset($variant_classes[$args['variant']])) {
            $args['classes'][] = $variant_classes[$args['variant']];
        }

        return $args;
    }
}
