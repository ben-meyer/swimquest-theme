<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Utils\TripData;

class TripCard extends ComponentBase
{
    protected static string $name = 'trip-card';

    public static function make(
        array $classes = [],
        ?\WP_Post $object = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['object']) && $args['object'] instanceof \WP_Post;
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];
        $post = $args['object'];
        $postId = $post->ID;

        $args['content'] = [
            'heading' => get_the_title($postId),
            'url' => get_the_permalink($postId),
            'image' => has_post_thumbnail($postId)
                ? ['ID' => get_post_thumbnail_id($postId), 'size' => 'gust_card_square']
                : null,
        ];

        // Meta items with icons
        $args['content']['meta'] = [];

        $dateLabel = TripData::getHeaderDateLabel($postId);
        if ($dateLabel) {
            $args['content']['meta'][] = [
                'icon' => 'icons/calendar.svg',
                'html' => esc_html($dateLabel),
            ];
        }

        $locationHtml = self::buildLocationHtml($postId);
        if ($locationHtml) {
            $args['content']['meta'][] = [
                'icon' => 'icons/location.svg',
                'html' => $locationHtml,
            ];
        }

        $skillLabel = TripData::getTaxonomyLabel($postId, 'skill_level');
        if ($skillLabel) {
            $args['content']['meta'][] = [
                'icon' => 'icons/skill-level.svg',
                'html' => esc_html($skillLabel),
            ];
        }

        // Price
        $cheapest = TripData::getCheapestPrice($postId);
        if ($cheapest !== null) {
            $args['content']['price'] = sprintf(__('From', 'gust').'%s£%s', "\n", number_format($cheapest, 0));
        }

        // Heading as nested component
        $args['content']['heading'] = [
            'heading' => $args['content']['heading'],
            'classes' => ['trip-card__heading'],
        ];

        if (! empty($args['content']['url'])) {
            $args['content']['heading']['link'] = $args['content']['url'];
        }

        $args['classes'][] = ! empty($args['content']['image']) ? 'has-image' : null;
        $args['classes'][] = ! empty($args['content']['url']) ? 'has-link' : null;

        return $args;
    }

    /**
     * Build location HTML with linked location and country terms.
     */
    protected static function buildLocationHtml(int $postId): ?string
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
                $parts[] = sprintf(
                    '<a href="%s" class="trip-card__meta-link">%s</a>',
                    esc_url($url),
                    esc_html($term->name)
                );
            } else {
                $parts[] = esc_html($term->name);
            }
        }

        return empty($parts) ? null : implode(', ', $parts);
    }
}
