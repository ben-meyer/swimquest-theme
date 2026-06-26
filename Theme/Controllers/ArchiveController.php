<?php

namespace Theme\Controllers;

use Gust\Components\NoContent;
use Gust\Components\Pagination;
use Gust\Components\TripCards;
use Gust\WordPress\PageObject;

class ArchiveController
{
    public static function renderLoop(): string
    {
        $object = PageObject::get();

        $items = [];
        while (\have_posts()) {
            \the_post();
            $items[]['object'] = \get_post();
        }

        \ob_start();

        if (! empty($items)) {
            echo TripCards::make(items: $items);
            echo Pagination::make();
        } elseif (\is_tax(['country', 'trip_style', 'location'])) {
            echo NoContent::make(content: ['message' => __('More trips coming soon...', 'gust')]);
        } else {
            echo NoContent::make(object: $object);
        }

        return \ob_get_clean();
    }
}
