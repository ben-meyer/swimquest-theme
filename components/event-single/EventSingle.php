<?php

namespace Gust\Components;

use Gust\ComponentBase;

class EventSingle extends ComponentBase
{
    protected static string $name = 'event-single';

    public static function make(
        ?\WP_Post $object = null,
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        $object = $args['object'] ?? \get_post();

        return $object instanceof \WP_Post && $object->post_type === 'events';
    }

    protected static function transform(array $args): array
    {
        $object = $args['object'] ?? \get_post();
        $postId = $object->ID;

        $args['post_id'] = $postId;
        $args['faqs'] = \get_field('faqs', $postId) ?: [];
        $args['intro_gallery'] = \get_field('intro_gallery', $postId) ?: [];
        $args['mid_gallery'] = \get_field('mid_gallery', $postId) ?: [];

        return $args;
    }
}
