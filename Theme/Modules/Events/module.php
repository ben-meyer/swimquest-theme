<?php

namespace Theme\Modules\Events;

use Gust\Components\Cards;
use Gust\Components\NoContent;
use Gust\Components\Pagination;
use Gust\Components\TaxonomyFilters;
use Gust\WordPress\PageObject;
use Theme\Utils\TripData;

class EventsModule
{
    public static function init(): void
    {
        PostType::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
        \add_action('pre_get_posts', [__CLASS__, 'setArchivePostsPerPage']);
        \add_action('pre_get_posts', [__CLASS__, 'filterArchiveToUpcomingEvents']);
    }

    public static function setArchivePostsPerPage(\WP_Query $query): void
    {
        if (\is_admin() || ! $query->is_main_query()) {
            return;
        }

        if ($query->is_post_type_archive('events')) {
            $query->set('posts_per_page', 12);
        }
    }

    /**
     * Restrict the events archive to upcoming events, ordered by nearest start
     * date ascending. Past events are excluded.
     */
    public static function filterArchiveToUpcomingEvents(\WP_Query $query): void
    {
        if (\is_admin() || ! $query->is_main_query()) {
            return;
        }

        if (! $query->is_post_type_archive('events')) {
            return;
        }

        $orderedIds = self::getUpcomingEventIds();

        if (empty($orderedIds)) {
            $query->set('post__in', [0]);

            return;
        }

        $query->set('post__in', $orderedIds);
        $query->set('orderby', 'post__in');
        $query->set('ignore_sticky_posts', true);
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
            echo TaxonomyFilters::make(object: $object);
            echo Cards::make(items: $items, columns: '3', card_type: 'trip-style', type: 'trip-style', default_read_more: 'View & Book');
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

    /**
     * @return int[]
     */
    protected static function getUpcomingEventIds(): array
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        $postIds = \get_posts([
            'post_type' => 'events',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => false,
            'suppress_filters' => false,
        ]);

        if (empty($postIds)) {
            return $cached = [];
        }

        \update_meta_cache('post', $postIds);

        $today = \current_time('Y-m-d');
        $items = [];

        foreach ($postIds as $postId) {
            $postId = (int) $postId;
            $nearest = self::getNearestUpcomingDate($postId, $today);

            if ($nearest === null) {
                continue;
            }

            $items[] = [
                'id' => $postId,
                'timestamp' => (int) \strtotime($nearest),
            ];
        }

        if (empty($items)) {
            return $cached = [];
        }

        \usort($items, static fn (array $a, array $b) => $a['timestamp'] <=> $b['timestamp']);

        return $cached = array_map(static fn (array $item) => $item['id'], $items);
    }

    protected static function getNearestUpcomingDate(int $postId, string $today): ?string
    {
        $nearest = null;

        foreach (TripData::getDateRows($postId) as $row) {
            if (empty($row['start_date']) || $row['start_date'] < $today) {
                continue;
            }

            if ($nearest === null || $row['start_date'] < $nearest) {
                $nearest = $row['start_date'];
            }
        }

        return $nearest;
    }
}
