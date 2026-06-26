<?php

namespace Theme\Controllers;

use Gust\Components\Cards;
use Gust\Components\NoContent;

class TripStylesController
{
    public static function renderContent(): string
    {
        $terms = \get_terms([
            'taxonomy' => 'trip_style',
            'hide_empty' => false,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ]);

        if (\is_wp_error($terms) || empty($terms)) {
            return (string) NoContent::make($content = ['message' => __('More Trips coming soon...', 'gust')]);
        }

        $items = array_map(fn ($term) => ['object' => $term], $terms);

        return (string) Cards::make(
            items: $items,
            columns: '3',
            card_type: 'trip-style',
            type: 'trip-style');

    }
}
