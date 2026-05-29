<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Cards Component
 *
 * Usage:
 *   use Gust\Components\Cards;
 *
 *   echo Cards::make();
 */
class Cards extends ComponentBase
{
    protected static string $name = 'cards';

    protected static function getDefaults(): array
    {
        return [
            'type' => 'default',
            'align' => 'full',
        ];
    }

    /**
     * Create a new Cards component.
     *
     * @param  string|null  $card_background_color  Programmatic-only: Background color for cards.
     * @param  array|null  $tag  Programmatic-only: Tags to filter by.
     * @param  string|null  $default_read_more  Programmatic-only: Default read more text.
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?string $type = null,
        array $items = [],
        mixed $link = null,
        ?string $align = null,
        array $classes = [],
        ?string $columns = null,
        ?string $card_type = null,
        ?bool $slider_on_mobile = null,
        ?string $default_read_more = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['card_source'])) {
            if ($args['card_source'] === 'custom') {
                if (! empty($args['custom_cards'])) {
                    foreach ($args['custom_cards'] as $card) {
                        $args['items'][] = ['content' => $card];
                    }
                }
            } else {
                if ($args['card_source'] === 'recent') {
                    $query = [
                        'post_type' => $args['post_type'],
                        'posts_per_page' => $args['limit'],
                        'exclude' => get_the_ID(),
                        'no_found_rows' => true,
                        'ignore_sticky_posts' => true,
                    ];

                    if (! empty($args['tag'])) {
                        $query['tag__in'] = $args['tag'];
                    }

                    $query = new \WP_Query($query);
                    $objects = $query->posts;
                } elseif ($args['card_source'] === 'selected') {
                    $objects = $args['selected'];
                } elseif ($args['card_source'] === 'trip_styles') {
                    if (! empty($args['selected_trip_styles'])) {
                        $objects = $args['selected_trip_styles'];
                    } else {
                        $objects = get_terms([
                            'taxonomy' => 'trip_style',
                            'hide_empty' => false,
                        ]);
                    }
                } elseif ($args['card_source'] === 'destinations') {
                    if (! empty($args['selected_destinations'])) {
                        $objects = $args['selected_destinations'];
                    } else {
                        $objects = get_terms([
                            'taxonomy' => 'country',
                            'hide_empty' => false,
                        ]);
                    }
                }

                if (! empty($objects)) {
                    foreach ($objects as $key => $object) {
                        $args['items'][$key] = ['object' => $object];
                    }
                }
            }
        }

        if (! empty($args['type']) && $args['type'] === 'horizontal') {
            $args['card_type'] = 'horizontal';
            $args['columns'] = '2';
        }

        if (empty($args['card_type']) && ($args['card_source'] ?? null) === 'trip_styles') {
            $args['card_type'] = 'trip-style';
        }

        if (! empty($args['button'])) {
            $args['button']['classes'] = ['btn'];
        }

        if (! empty($args['items'])) {
            foreach ($args['items'] as $key => $card) {
                $args['items'][$key] = array_merge(['type' => $args['card_type'] ?? ''], $args['items'][$key]);

                if (\Gust\Helpers::isTaxonomy()) {
                    $args['items'][$key]['classes'][] = 'align-none';
                }

                if (! empty($args['card_background_color']) && $args['card_background_color'] !== 'default') {
                    $args['items'][$key]['background'] = $args['card_background_color'];
                }

                if (! empty($args['card_image_fit']) && $args['card_image_fit'] !== 'default') {
                    $args['items'][$key]['image_fit'] = $args['card_image_fit'];
                }

                if (
                    ($args['card_source'] ?? null) === 'custom'
                    && ! empty($args['items'][$key]['content']['image'])
                    && empty($args['items'][$key]['image_size'])
                ) {
                    $args['items'][$key]['image_size'] = 'gust_card_square';
                }

                if ($args['type'] === 'horizontal') {
                    $args['items'][$key]['show_read_more'] = false;
                }
            }
        }

        // Set read-more text based on card source or type.
        // "Find Your Trip" for trip styles and destinations, "Read More" for everything else.
        $default_read_more = 'Read More';
        $cardSource = $args['card_source'] ?? null;
        $cardType = $args['card_type'] ?? null;
        if ($cardSource === 'trip_styles' || $cardSource === 'destinations' || $cardType === 'trip-style') {
            $default_read_more = $args['default_read_more'] ?? 'Find Your Trip';
        }

        if (! empty($args['items'])) {
            foreach ($args['items'] as $key => $card) {
                if (empty($args['items'][$key]['read_more_text'])) {
                    $args['items'][$key]['read_more_text'] = $default_read_more;
                }
            }
        }

        if (! empty($args['columns']) && $args['columns'] !== 'default') {
            $args['classes'][] = 'cards--columns-'.$args['columns'];
        }

        $args['classes'][] = 'cards--type--'.($args['type'] ?? 'default');
        $args['classes'][] = ($args['card_source'] ?? null) === 'custom' ? 'cards--source--custom' : null;

        // slider_on_mobile is hidden when type is carousel as the swiper handles it.
        $args['classes'][] = ! empty($args['slider_on_mobile']) ? 'cards--slider-on-mobile' : null;

        return $args;
    }
}
