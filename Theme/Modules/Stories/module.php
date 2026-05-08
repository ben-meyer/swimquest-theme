<?php

namespace Theme\Modules\Stories;

use Gust\Components\Cards;
use Gust\Components\NoContent;
use Gust\Components\Pagination;
use Gust\Router;
use Gust\WordPress\PageObject;

class StoriesModule
{
    public static function init(): void
    {
        PostType::init();

        Router::decoratePostType('story', static::class)
            ->withPage('stories')
            ->withSlot('template-content', [static::class, 'renderArchive']);

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
    }

    public static function renderArchive(): string
    {
        $object = PageObject::get();

        $items = [];
        while (\have_posts()) {
            \the_post();
            $items[]['object'] = \get_post();
        }

        \ob_start();

        if (! empty($items)) {
            echo Cards::make(items: $items, type: 'horizontal');
            echo Pagination::make();
        } else {
            echo NoContent::make(object: $object);
        }

        return \ob_get_clean();
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
