<?php

namespace Gust\Components;

use Gust\ComponentBase;

class GetInTouch extends ComponentBase
{
    protected static string $name = 'get-in-touch';

    public static function make(
        array $contacts = [],
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args['classes'] = array_merge(['get-in-touch'], $args['classes'] ?? []);

        $contacts = is_array($args['contacts'] ?? null) ? $args['contacts'] : [];

        $args['contacts'] = array_map(fn (array $row) => [
            'icon' => $row['icon'] ?? 'phone',
            'label' => $row['label'] ?? '',
            'value' => $row['value'] ?? '',
            'url' => $row['url'] ?? '',
        ], array_filter($contacts, 'is_array'));

        return $args;
    }
}
