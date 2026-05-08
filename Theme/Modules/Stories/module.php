<?php

namespace Theme\Modules\Stories;

use Gust\Components\Cards;
use Gust\Components\NoContent;
use Gust\Components\Pagination;
use Gust\Router;
use Gust\Router\RouterPage;
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
        \add_filter('wpseo_breadcrumb_links', [__CLASS__, 'filterBreadcrumbLinks']);
    }

    /**
     * Replace the post-type archive crumb on single stories with the
     * "stories" router page (e.g. "News & Stories"), so the breadcrumb
     * trail uses the editorial page title rather than the CPT label/slug.
     */
    public static function filterBreadcrumbLinks(array $links): array
    {
        if (! \is_singular('story')) {
            return $links;
        }

        $page = RouterPage::getPageByRole('stories');
        if (! $page instanceof \WP_Post) {
            return $links;
        }

        foreach ($links as $i => $link) {
            if (isset($link['ptarchive']) && $link['ptarchive'] === 'story') {
                $links[$i] = [
                    'text' => \get_the_title($page),
                    'url' => \get_permalink($page),
                    'id' => $page->ID,
                ];
                break;
            }
        }

        return $links;
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
