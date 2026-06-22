<?php

namespace Theme\Modules\Trips;

use Theme\Utils\TripData;

class TripsModule
{
    protected const ORDERED_TAXONOMIES = ['trip_style', 'country', 'location'];

    protected const ORDERED_POST_TYPES = ['trip', 'events'];

    public static function init(): void
    {
        PostType::init();
        TripStyleTaxonomy::init();
        SkillLevelTaxonomy::init();
        SwimTypeTaxonomy::init();
        CountryTaxonomy::init();
        LocationTaxonomy::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
        \add_action('pre_get_posts', [__CLASS__, 'orderTaxonomyArchives']);
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }

    /**
     * Trip + events taxonomy archives (trip_style, country, location) order
     * posts by the nearest upcoming departure date, ascending. Posts with no
     * upcoming date are excluded.
     *
     * Skipped when Simple Custom Post Order is configured to manage ordering
     * for the trip or events post type — the plugin's manual order then wins.
     */
    public static function orderTaxonomyArchives(\WP_Query $query): void
    {
        if (\is_admin() || ! $query->is_main_query()) {
            return;
        }

        if (! $query->is_tax(self::ORDERED_TAXONOMIES)) {
            return;
        }

        $query->set('posts_per_page', 12);

        if (self::scpoManagesPostOrder()) {
            return;
        }

        $term = $query->get_queried_object();

        if (! $term instanceof \WP_Term) {
            return;
        }

        $orderedIds = TripData::getUpcomingPostIds([
            'post_type' => self::ORDERED_POST_TYPES,
            'tax_query' => [
                [
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ],
            ],
        ]);

        if (empty($orderedIds)) {
            $query->set('post__in', [0]);

            return;
        }

        $query->set('post__in', $orderedIds);
        $query->set('orderby', 'post__in');
        $query->set('ignore_sticky_posts', true);
    }

    protected static function scpoManagesPostOrder(): bool
    {
        $options = \get_option('scporder_options', []);
        $managedPostTypes = $options['objects'] ?? [];

        return ! empty(array_intersect((array) $managedPostTypes, self::ORDERED_POST_TYPES));
    }
}
