<?php

namespace Gust\Components;

use Gust\ComponentBase;

class RelatedStories extends ComponentBase
{
    protected static string $name = 'related-stories';

    protected static int $maxItems = 2;

    public static function make(
        string $heading = '',
        array $stories = [],
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['stories']);
    }

    protected static function transform(array $args): array
    {
        $stories = array_slice($args['stories'] ?? [], 0, static::$maxItems);

        $args['heading'] = ! empty($args['heading']) ? $args['heading'] : __('Related stories', 'gust');
        $args['items'] = array_map(fn ($story) => ['object' => $story], $stories);

        return $args;
    }
}
