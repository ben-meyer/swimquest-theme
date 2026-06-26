<?php

namespace Theme\Controllers;

use Gust\Components\Cards;
use Gust\Components\NoContent;

class DestinationsController
{
    public static function renderContent(): string
    {
        $terms = \get_terms([
            'taxonomy' => 'country',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => 12,
        ]);

        if (\is_wp_error($terms) || empty($terms)) {
            return (string) NoContent::make($content = ['message' => __('More Trips coming soon...', 'gust')]);
        }

        $items = array_map(fn ($term) => ['object' => $term], $terms);

        return (string) Cards::make(
            items: $items,
            columns: '3',
            card_type: 'trip-style',
            type: 'trip-style',
        );
    }
}
