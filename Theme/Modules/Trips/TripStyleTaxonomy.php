<?php

namespace Theme\Modules\Trips;

class TripStyleTaxonomy
{
    protected const SLUG = 'trip_style';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'register']);
        \add_filter('gust/templates/taxonomies', [__CLASS__, 'filterGustTemplatesTaxonomies']);
        \add_action('admin_enqueue_scripts', [__CLASS__, 'removeNativeDescriptionField']);
    }

    public static function register(): void
    {
        if (! function_exists('register_extended_taxonomy')) {
            return;
        }

        \register_extended_taxonomy(
            self::SLUG,
            ['trip', 'events'],
            [
                'hierarchical' => false,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'meta_box' => 'simple',
                'rewrite' => ['slug' => 'trip-styles'],
            ],
            [
                'singular' => __('Trip Style', 'gust'),
                'plural' => __('Trip Styles', 'gust'),
                'slug' => 'trip-styles',
            ]
        );
    }

    public static function filterGustTemplatesTaxonomies(array $taxonomies): array
    {
        $taxonomies[] = self::SLUG;

        return $taxonomies;
    }

    /**
     * Remove the native WP description field from the term form.
     * The ACF subheading field replaces it.
     *
     * WordPress has no PHP hook to remove core form fields, so we remove
     * the element from the DOM via an inline script on the correct screen.
     */
    public static function removeNativeDescriptionField(): void
    {
        $screen = \get_current_screen();
        if (! $screen || $screen->taxonomy !== self::SLUG) {
            return;
        }

        \wp_add_inline_script('wp-util', '
            document.addEventListener("DOMContentLoaded", function () {
                var el = document.querySelector(".term-description-wrap");
                if (el) el.parentNode.removeChild(el);
            });
        ');
    }
}
