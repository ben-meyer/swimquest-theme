<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * TestimonialCards Component
 *
 * A 3-column grid of testimonial cards. Two source modes:
 *   - 'custom'  — editor-entered quotes, author, stars
 *   - 'stories' — selected story posts with manually set stars
 *
 * Usage:
 *   use Gust\Components\TestimonialCards;
 *
 *   echo TestimonialCards::make(
 *       heading: 'What our swimmers say',
 *       items: [
 *           ['stars' => 5, 'quote' => 'Amazing trip!', 'author_name' => 'Jane S.', 'author_detail' => 'London'],
 *       ],
 *   );
 */
class TestimonialCards extends ComponentBase
{
    protected static string $name = 'testimonial-cards';

    protected static function getDefaults(): array
    {
        return [
            'align' => 'full',
        ];
    }

    public static function make(
        array $classes = [],
        array $items = [],
        ?string $heading = null,
        ?string $subheading = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['items']) || ! empty($args['card_source']);
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];
        $args['items'] ??= [];

        $source = $args['card_source'] ?? null;

        if ($source === 'custom' && ! empty($args['custom_items'])) {
            foreach ($args['custom_items'] as $item) {
                $args['items'][] = [
                    'stars'         => (int) ($item['stars'] ?? 5),
                    'quote'         => $item['quote'] ?? '',
                    'author_name'   => $item['author_name'] ?? '',
                    'author_detail' => $item['author_detail'] ?? '',
                    'image'         => ! empty($item['image']) ? $item['image'] : null,
                ];
            }
        } elseif ($source === 'stories' && ! empty($args['story_items'])) {
            foreach ($args['story_items'] as $item) {
                $post = $item['story'] ?? null;

                if (! ($post instanceof \WP_Post)) {
                    continue;
                }

                $args['items'][] = [
                    'stars'       => (int) ($item['stars'] ?? 5),
                    'quote'       => get_the_title($post->ID),
                    'author_name' => get_field('author_name', $post->ID) ?: get_the_author_meta('display_name', $post->post_author),
                    'url'         => get_the_permalink($post->ID),
                    'image'       => has_post_thumbnail($post->ID)
                        ? ['ID' => get_post_thumbnail_id($post->ID), 'size' => 'thumbnail']
                        : null,
                ];
            }
        }

        if (! empty($args['button'])) {
            $args['button']['classes'] = ['btn', 'btn--theme-2'];
        }

        return $args;
    }
}
