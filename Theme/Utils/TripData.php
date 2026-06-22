<?php

namespace Theme\Utils;

class TripData
{
    public static function getDateRows(int $postId): array
    {
        $rows = \get_field('dates', $postId) ?: [];

        return array_values(array_filter(array_map(function (array $row) {
            $start = $row['start_date'] ?? '';
            $end = $row['end_date'] ?? '';

            if (empty($start) && empty($end)) {
                return null;
            }

            $price = isset($row['price']) && $row['price'] !== '' ? (float) $row['price'] : null;
            $status = $row['status'] ?? 'bookable';

            return [
                'start_date' => $start,
                'end_date' => $end,
                'label' => self::formatDateRange($start, $end),
                'price' => $price,
                'price_display' => $price !== null ? '£'.number_format($price, 0) : null,
                'status' => $status,
                'status_label' => self::getStatusLabel($status),
                'booking_url' => $row['booking_url'] ?? '',
                'enquiry_url' => $row['enquiry_url'] ?? '',
            ];
        }, $rows)));
    }

    public static function hasMultipleDates(int $postId): bool
    {
        return count(self::getDateRows($postId)) > 1;
    }

    public static function getNearestUpcomingDate(int $postId, ?string $today = null): ?string
    {
        $today ??= \current_time('Y-m-d');
        $nearest = null;

        foreach (self::getDateRows($postId) as $row) {
            if (empty($row['start_date']) || $row['start_date'] < $today) {
                continue;
            }

            if ($nearest === null || $row['start_date'] < $nearest) {
                $nearest = $row['start_date'];
            }
        }

        return $nearest;
    }

    /**
     * Return post IDs matching the given get_posts() args that have at least
     * one upcoming date row, ordered by nearest start_date ascending.
     *
     * @param  array<string, mixed>  $queryArgs
     * @return int[]
     */
    public static function getUpcomingPostIds(array $queryArgs, ?string $today = null): array
    {
        $defaults = [
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => false,
            'suppress_filters' => false,
        ];

        $postIds = \get_posts(array_merge($defaults, $queryArgs));

        if (empty($postIds)) {
            return [];
        }

        \update_meta_cache('post', $postIds);

        $today ??= \current_time('Y-m-d');
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

        \usort($items, static fn (array $a, array $b) => $a['timestamp'] <=> $b['timestamp']);

        return array_map(static fn (array $item) => $item['id'], $items);
    }

    public static function getHeaderDateLabel(int $postId): ?string
    {
        $rows = self::getDateRows($postId);

        if (empty($rows)) {
            return null;
        }

        if (count($rows) > 1) {
            return __('Multiple dates', 'gust');
        }

        return $rows[0]['label'];
    }

    public static function getCheapestPrice(int $postId): ?float
    {
        $prices = array_values(array_filter(array_map(
            fn (array $row) => $row['price'],
            self::getDateRows($postId)
        ), fn ($price) => $price !== null));

        if (empty($prices)) {
            return null;
        }

        return min($prices);
    }

    public static function getPriceFromLabel(int $postId): ?string
    {
        $price = self::getCheapestPrice($postId);

        if ($price === null) {
            return null;
        }

        return sprintf(__('From £%spp', 'gust'), number_format($price, 0));
    }

    public static function getLocationLabel(int $postId): ?string
    {
        $parts = [];

        $locations = \wp_get_post_terms($postId, 'location');
        $countries = \wp_get_post_terms($postId, 'country');

        if (! empty($locations) && ! \is_wp_error($locations)) {
            $parts[] = $locations[0]->name;
        }

        if (! empty($countries) && ! \is_wp_error($countries)) {
            $parts[] = $countries[0]->name;
        }

        return empty($parts) ? null : implode(', ', $parts);
    }

    public static function getLocationHtml(int $postId): ?string
    {
        $parts = [];

        foreach (['location', 'country'] as $taxonomy) {
            $terms = \wp_get_post_terms($postId, $taxonomy);

            if (empty($terms) || \is_wp_error($terms)) {
                continue;
            }

            $term = $terms[0];
            $url = \get_term_link($term);

            if (! \is_wp_error($url)) {
                $parts[] = sprintf('<a href="%s">%s</a>', \esc_url($url), \esc_html($term->name));
            } else {
                $parts[] = \esc_html($term->name);
            }
        }

        return empty($parts) ? null : implode(',&nbsp;', $parts);
    }

    public static function getTaxonomyLabel(int $postId, string $taxonomy): ?string
    {
        $terms = \wp_get_post_terms($postId, $taxonomy);

        if (empty($terms) || \is_wp_error($terms)) {
            return null;
        }

        return implode(', ', array_map(fn ($term) => $term->name, $terms));
    }

    public static function getPrimaryEnquiryAction(int $postId): ?array
    {
        $rows = self::getDateRows($postId);

        $enquiryRows = array_values(array_filter($rows, fn ($row) => ! empty($row['enquiry_url'])));

        if (empty($enquiryRows)) {
            $tripUrl = \get_field('trip_enquiry_url', $postId);

            if (! empty($tripUrl)) {
                return [
                    'label' => __('Enquire', 'gust'),
                    'url' => $tripUrl,
                    'target' => '_blank',
                    'is_link' => true,
                ];
            }

            return null;
        }

        if (count($rows) !== 1) {
            return [
                'label' => __('Enquire', 'gust'),
                'url' => '#trip-dates',
                'is_link' => true,
            ];
        }

        return [
            'label' => __('Enquire', 'gust'),
            'url' => $enquiryRows[0]['enquiry_url'],
            'target' => '_blank',
            'is_link' => true,
        ];
    }

    public static function getPrimaryBookingAction(int $postId): ?array
    {
        $rows = self::getDateRows($postId);

        if (empty($rows)) {
            if (self::getPrimaryEnquiryAction($postId)) {
                return null;
            }

            return [
                'label' => __('Coming Soon', 'gust'),
                'url' => null,
                'is_link' => false,
            ];
        }

        if (count($rows) !== 1) {
            return [
                'label' => __('View dates & book', 'gust'),
                'url' => '#trip-dates',
                'is_link' => true,
            ];
        }

        $row = $rows[0];

        if ($row['status'] === 'bookable' && ! empty($row['booking_url'])) {
            return [
                'label' => __('Book', 'gust'),
                'url' => $row['booking_url'],
                'target' => '_blank',
                'is_link' => true,
            ];
        }

        if (\in_array($row['status'], ['sold_out', 'sold_out_private'], true)) {
            return [
                'label' => $row['status'] === 'sold_out_private'
                    ? __('Private Group', 'gust')
                    : __('Sold Out', 'gust'),
                'url' => null,
                'is_link' => false,
            ];
        }

        if (self::getPrimaryEnquiryAction($postId)) {
            return null;
        }

        return [
            'label' => __('Coming Soon', 'gust'),
            'url' => null,
            'is_link' => false,
        ];
    }

    public static function getSectionMap(int $postId): array
    {
        $sections = [
            'trip-highlights' => [
                'label' => __('Highlights', 'gust'),
                'show' => ! empty(\get_field('highlights', $postId)),
            ],
            'trip-itinerary' => [
                'label' => __('Itinerary', 'gust'),
                'show' => ! empty(\get_field('itinerary', $postId)),
            ],
            'trip-accommodation' => [
                'label' => __('Accommodation', 'gust'),
                'show' => ! empty(\get_field('accommodation', $postId)),
            ],
            'trip-includes' => [
                'label' => __("What's included", 'gust'),
                'show' => ! empty(\get_field('included_items', $postId)) || ! empty(\get_field('not_included_items', $postId)),
            ],
            'trip-getting-there' => [
                'label' => __('Getting there', 'gust'),
                'show' => ! empty(\get_field('getting_there_stages', $postId)),
            ],
            'trip-reviews' => [
                'label' => __('Reviews', 'gust'),
                'show' => ! empty(\get_field('reviews_embed_code', $postId)),
            ],
            'trip-faqs' => [
                'label' => __('FAQs', 'gust'),
                'show' => ! empty(\get_field('faqs', $postId)),
            ],
        ];

        return array_values(array_filter(array_map(function (string $id, array $section) {
            if (! $section['show']) {
                return null;
            }

            return [
                'id' => $id,
                'label' => $section['label'],
                'url' => '#'.$id,
            ];
        }, array_keys($sections), $sections)));
    }

    protected static function formatDateRange(string $start, string $end): string
    {
        if (empty($start)) {
            return '';
        }

        $startTs = \strtotime($start);
        $endTs = $end ? \strtotime($end) : null;

        if (! $endTs || \date('Y-m-d', $startTs) === \date('Y-m-d', $endTs)) {
            return \date_i18n('j M Y', $startTs);
        }

        $sameYear = \date('Y', $startTs) === \date('Y', $endTs);
        $fmtStart = \date_i18n($sameYear ? 'j M' : 'j M Y', $startTs);
        $fmtEnd = \date_i18n('j M Y', $endTs);

        return $fmtStart.' – '.$fmtEnd;
    }

    protected static function getStatusLabel(string $status): string
    {
        return match ($status) {
            'sold_out' => __('Sold Out', 'gust'),
            'sold_out_private' => __('Private Group', 'gust'),
            default => __('Book Now', 'gust'),
        };
    }
}
