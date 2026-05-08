<?php

namespace Gust\Components;

use Gust\ComponentBase;

class TripCards extends ComponentBase
{
    protected static string $name = 'trip-cards';

    protected static function getDefaults(): array
    {
        return [
            'align' => 'full',
        ];
    }

    public static function make(
        array $items = [],
        ?string $align = null,
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];

        if (! empty($args['card_source'])) {
            if ($args['card_source'] === 'recent') {
                $query = new \WP_Query([
                    'post_type' => 'trip',
                    'posts_per_page' => $args['limit'] ?? 3,
                    'exclude' => get_the_ID(),
                    'no_found_rows' => true,
                    'ignore_sticky_posts' => true,
                ]);

                $objects = $query->posts;
            } elseif ($args['card_source'] === 'selected') {
                $objects = $args['selected'] ?? [];
            }

            if (! empty($objects)) {
                foreach ($objects as $object) {
                    $args['items'][] = ['object' => $object];
                }
            }
        }

        if (! empty($args['button'])) {
            $args['button']['classes'] = ['btn'];
        }

        return $args;
    }
}
