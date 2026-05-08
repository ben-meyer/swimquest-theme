<?php

namespace Gust\Components;

use Gust\ComponentBase;

class ImageFullWidth extends ComponentBase
{
    protected static string $name = 'image-full-width';

    public static function make(
        array $classes = [],
        mixed $id = null,
        ?string $title = null,
        ?string $alt = null,
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        // Map ACF 'image' field to 'id'
        if (! empty($args['image']) && empty($args['id'])) {
            $args['id'] = $args['image'];
        }

        if (! empty($args['ID'])) {
            $args['id'] = $args['ID'];
        }

        if (! empty($args['sizes']) && ! is_array($args['sizes'])) {
            $args['attributes']['sizes'] = $args['sizes'];
        }

        // Always apply full_width behavior
        $args['size'] = 'full_width';

        $existingClass = trim($args['attributes']['class'] ?? '');
        $args['attributes']['class'] = trim(($existingClass ? $existingClass . ' ' : '') . 'alignfull h-[600px] object-cover block');

        $existingStyle = trim($args['attributes']['style'] ?? '');
        $args['attributes']['style'] = trim(($existingStyle ? $existingStyle . '; ' : '') . 'width:100%;height:min(600px,100vw);object-fit:cover;display:block;');

        if (empty($args['attributes']['sizes'])) {
            $args['attributes']['sizes'] = '100vw';
        }

        if (! empty($args['id'])) {
            $args['output'] = wp_get_attachment_image(
                $args['id'],
                $args['size'],
                false,
                $args['attributes']
            );
        }

        return $args;
    }
}
