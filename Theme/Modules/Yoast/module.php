<?php

namespace Theme\Modules\Yoast;

use Gust\Router;
use Gust\Router\RouterPage;

class YoastModule
{
    public static function init(): void
    {
        \add_theme_support('yoast-seo-breadcrumbs');
        \add_filter('wpseo_metabox_prio', [__CLASS__, 'priority']);
        \add_filter('wpseo_breadcrumb_separator', [__CLASS__, 'breadcrumbSeparator']);
        \add_filter('wpseo_breadcrumb_output_class', [__CLASS__, 'breadcrumbWrapperClass']);
        \add_filter('wpseo_breadcrumb_links', [__CLASS__, 'swapRouterArchiveLinks']);
    }

    public static function priority(): string
    {
        return 'low';
    }

    public static function breadcrumbSeparator(string $markup): string
    {
        return '<span class="breadcrumbs__yoast-separator"></span>';
    }

    public static function breadcrumbWrapperClass(string $class): string
    {
        return 'breadcrumbs__yoast-wrapper';
    }

    /**
     * Replace any post-type archive crumb whose post type is decorated by
     * a router page with the router page's title and permalink. Lets the
     * editorial title (e.g. "News & Stories") show in the trail instead of
     * the CPT label/slug, for every CPT registered via
     * Router::decoratePostType()->withPage().
     */
    public static function swapRouterArchiveLinks(array $links): array
    {
        $map = static::routerArchiveMap();
        if (! $map) {
            return $links;
        }

        foreach ($links as $i => $link) {
            $postType = $link['ptarchive'] ?? null;
            if (! $postType || ! isset($map[$postType])) {
                continue;
            }

            $page = $map[$postType];
            $links[$i] = [
                'text' => \get_the_title($page),
                'url' => \get_permalink($page),
                'id' => $page->ID,
            ];
        }

        return $links;
    }

    /** @return array<string, \WP_Post> post_type => router page */
    protected static function routerArchiveMap(): array
    {
        if (! class_exists(Router::class)) {
            return [];
        }

        $map = [];
        foreach (Router::getRoutes()->getDecorated() as $route) {
            $role = $route->getRole();
            if (! $role) {
                continue;
            }

            $parts = explode(':', $route->getPattern(), 2);
            if (($parts[0] ?? '') !== 'post_type' || empty($parts[1])) {
                continue;
            }

            $page = RouterPage::getPageByRole($role);
            if ($page instanceof \WP_Post) {
                $map[$parts[1]] = $page;
            }
        }

        return $map;
    }
}
