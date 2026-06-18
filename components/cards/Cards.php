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
        // --------------------------------------------------------------------
        // STEP 1 — Build the list of cards from the chosen "Card Content" source.
        //
        // The editor picks ONE source in the Cards block sidebar:
        //   - custom        → editor writes each card by hand (heading/image/text/link)
        //   - recent        → latest N posts of a given post_type
        //   - selected      → specific posts the editor picked
        //   - trip_styles   → terms from the trip_style taxonomy
        //   - destinations  → terms from the country taxonomy
        //
        // The output of this step is $args['items'] — an array of per-card data.
        // Custom cards are stored as ['content' => <raw editor fields>].
        // All other sources store as ['object' => <WP_Post or WP_Term>] and
        // Card.php turns the object into the card content later.
        // --------------------------------------------------------------------
        if (! empty($args['card_source'])) {
            if ($args['card_source'] === 'custom') {
                // Editor-authored cards: pass the raw ACF row through as `content`.
                if (! empty($args['custom_cards'])) {
                    foreach ($args['custom_cards'] as $card) {
                        $args['items'][] = ['content' => $card];
                    }
                }
            } else {
                // Anything that isn't custom resolves to a list of WP objects.
                if ($args['card_source'] === 'recent') {
                    // Pull the latest posts of the chosen type, excluding the
                    // current page so a post never lists itself.
                    $query = [
                        'post_type' => $args['post_type'],
                        'posts_per_page' => $args['limit'],
                        'exclude' => get_the_ID(),
                        'no_found_rows' => true,
                        'ignore_sticky_posts' => true,
                    ];

                    // Optional tag filter passed in programmatically.
                    if (! empty($args['tag'])) {
                        $query['tag__in'] = $args['tag'];
                    }

                    $query = new \WP_Query($query);
                    $objects = $query->posts;
                } elseif ($args['card_source'] === 'selected') {
                    // Editor hand-picked these posts via the relationship field.
                    $objects = $args['selected'];
                } elseif ($args['card_source'] === 'trip_styles') {
                    // If the editor picked specific trip-style terms use those;
                    // otherwise fall back to showing every trip_style term.
                    if (! empty($args['selected_trip_styles'])) {
                        $objects = $args['selected_trip_styles'];
                    } else {
                        $objects = get_terms([
                            'taxonomy' => 'trip_style',
                            'hide_empty' => false,
                        ]);
                    }
                } elseif ($args['card_source'] === 'destinations') {
                    // Same idea for the country (destination) taxonomy.
                    if (! empty($args['selected_destinations'])) {
                        $objects = $args['selected_destinations'];
                    } else {
                        $objects = get_terms([
                            'taxonomy' => 'country',
                            'hide_empty' => false,
                        ]);
                    }
                }

                // Wrap each WP object so Card.php can detect & expand it.
                if (! empty($objects)) {
                    foreach ($objects as $key => $object) {
                        $args['items'][$key] = ['object' => $object];
                    }
                }
            }
        }

        // --------------------------------------------------------------------
        // STEP 2 — Decide the card visual "type" (horizontal / trip-style / etc).
        //
        // The block has a "Type" control with: Default / Horizontal / Carousel.
        // We also infer trip-style cards automatically when the source is
        // trip_styles, so the editor doesn't have to set it twice.
        // --------------------------------------------------------------------

        // "Horizontal" forces the horizontal card layout and a 2-column grid.
        if (! empty($args['type']) && $args['type'] === 'horizontal') {
            $args['card_type'] = 'horizontal';
            $args['columns'] = '2';
        }

        // Trip-style cards have a distinct look; auto-apply when source matches.
        if (empty($args['card_type']) && ($args['card_source'] ?? null) === 'trip_styles') {
            $args['card_type'] = 'trip-style';
        }

        // Top-of-block "Button" (an ACF link field above the cards). When set,
        // it inherits the standard `btn` class so it picks up brand styling.
        if (! empty($args['button'])) {
            $args['button']['classes'] = ['btn'];
        }

        // --------------------------------------------------------------------
        // STEP 3 — Per-card decoration: apply block-level options to each card.
        //
        // Everything in this loop is the block's settings cascading down onto
        // the individual cards built in Step 1.
        // --------------------------------------------------------------------
        if (! empty($args['items'])) {
            foreach ($args['items'] as $key => $card) {
                // Stamp the chosen card_type onto every card (e.g. 'horizontal').
                $args['items'][$key] = array_merge(['type' => $args['card_type'] ?? ''], $args['items'][$key]);

                // Cards rendered inside a taxonomy archive should sit flush in
                // the grid, not honour any per-card alignment.
                if (\Gust\Helpers::isTaxonomy()) {
                    $args['items'][$key]['classes'][] = 'align-none';
                }

                // Block-wide background colour override (e.g. all cards navy).
                if (! empty($args['card_background_color']) && $args['card_background_color'] !== 'default') {
                    $args['items'][$key]['background'] = $args['card_background_color'];
                }

                // Block-wide image fit override (cover vs contain).
                if (! empty($args['card_image_fit']) && $args['card_image_fit'] !== 'default') {
                    $args['items'][$key]['image_fit'] = $args['card_image_fit'];
                }

                // Custom cards with an editor-uploaded image: default the image
                // to the square crop unless an explicit size was passed in.
                if (
                    ($args['card_source'] ?? null) === 'custom'
                    && ! empty($args['items'][$key]['content']['image'])
                    && empty($args['items'][$key]['image_size'])
                ) {
                    $args['items'][$key]['image_size'] = 'gust_card_square';
                }

                // Horizontal cards never show a read-more button (the whole row
                // acts as the call to action).
                if ($args['type'] === 'horizontal') {
                    $args['items'][$key]['show_read_more'] = false;
                }
            }
        }

        // --------------------------------------------------------------------
        // STEP 4 — Choose the default read-more text for any card that didn't
        // get one of its own.
        //
        // Trip styles & destinations push people toward booking, so they read
        // "Find Your Trip". Everything else reads "Read More". Card.php uses
        // this as the fallback when a custom card has a link but no link title.
        // --------------------------------------------------------------------
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

        // --------------------------------------------------------------------
        // STEP 5 — Add CSS hook classes to the outer wrapper for layout.
        // --------------------------------------------------------------------

        // Column count modifier (cards--columns-2/3/4).
        if (! empty($args['columns']) && $args['columns'] !== 'default') {
            $args['classes'][] = 'cards--columns-'.$args['columns'];
        }

        // Type modifier (cards--type--default / --horizontal / --carousel).
        $args['classes'][] = 'cards--type--'.($args['type'] ?? 'default');

        // Source modifier — only set for custom so styles can target the
        // editor-built variant specifically (e.g. partnerships page).
        $args['classes'][] = ($args['card_source'] ?? null) === 'custom' ? 'cards--source--custom' : null;

        // Mobile horizontal scroll. Hidden when type is carousel because
        // Swiper already handles mobile sliding for carousels.
        $args['classes'][] = ! empty($args['slider_on_mobile']) ? 'cards--slider-on-mobile' : null;

        return $args;
    }
}
