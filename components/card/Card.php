<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Card Component
 *
 * Usage:
 *   use Gust\Components\Card;
 *
 *   echo Card::make();
 */
class Card extends ComponentBase
{
    protected static string $name = 'card';

    protected static function getDefaults(): array
    {
        return [
            'background' => 'white',
            'image_size' => 'gust_card_square',
            'show_read_more' => true,
            'heading_class' => 'is-style-type-h4',
        ];
    }

    /**
     * Create a new Card component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $attributes = [],
        array $classes = [],
        ?string $type = null,
        ?string $background = null,
        ?string $image_size = null,
        ?bool $show_read_more = null,
        ?string $heading_class = null,
        array $content = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];

        // Hold onto the caller's original type so we can re-apply it as a
        // class later even if we overwrite $args['type'] based on the object
        // (e.g. caller passed type=horizontal, post_type=story changes it to
        // 'story' — we still want g-card--type--horizontal on the wrapper).
        $incoming_type = $args['type'] ?? null;

        // ====================================================================
        // BRANCH A — Card built from a WP object (post or term).
        // Cards.php uses this for: recent / selected / trip_styles / destinations.
        // ====================================================================
        if (! empty($args['object'])) {
            $object = $args['object'];

            // ---- Posts: title, excerpt, featured image, category label ----
            if ($args['object'] instanceof \WP_Post) {
                $args['content'] = [
                    'heading' => get_the_title($object->ID),
                    'url' => get_the_permalink($object->ID),
                    'text' => get_the_excerpt($object->ID),
                    'meta' => '',
                    // First category becomes a chip on the card.
                    'labels' => \Theme\Utils\ObjectMeta::getObjectLabels($object->ID, [
                        'limit' => 1,
                        'taxonomies' => ['category'],
                    ]),
                ];

                if (has_post_thumbnail($object->ID)) {
                    $args['content']['image'] = ['ID' => get_post_thumbnail_id($object->ID)];
                }

                // No hand-written excerpt? Use the page-header intro field
                // instead — it usually reads better than a stripped-prose excerpt.
                if (! has_excerpt($object->ID)) {
                    if ($page_header_content = get_field('page_header_content', $object->ID)) {
                        $args['content']['text'] = $page_header_content;
                    }
                }

                // ---- Per-post-type tweaks ----------------------------------
                // News article: date + author byline instead of excerpt, no
                // read-more (whole card is clickable).
                if ($object->post_type === 'post') {
                    $args['type'] = 'article';
                    $args['show_read_more'] = false;
                    $args['content']['text'] = '';
                    $args['heading_class'] = 'is-style-type-h4';

                    $metaDate = \Theme\Utils\ObjectMeta::getObjectDate($object);
                    $metaAuthor = \Theme\Utils\ObjectMeta::getObjectAuthor($object);

                    $args['content']['meta'] .= $metaDate ?? null;
                    // Space between date and "by author" when both are present.
                    $args['content']['meta'] .= $metaDate && $metaAuthor ? ' ' : null;

                    if (! empty($metaAuthor)) {
                        // Author may or may not have a URL (linked profile vs plain name).
                        $metaAuthor = ! empty($metaAuthor['url'])
                            ? Link::make(...$metaAuthor)
                            : esc_html($metaAuthor['title']);
                        $args['content']['meta'] .= sprintf(__('by %s', 'gust'), $metaAuthor);
                    }
                } elseif ($object->post_type === 'story') {
                    // Story: same author byline pattern as article, but keeps
                    // the excerpt and uses its own visual type.
                    $args['type'] = 'story';
                    $args['show_read_more'] = false;
                    $args['heading_class'] = 'is-style-type-h4';

                    $metaAuthor = \Theme\Utils\ObjectMeta::getObjectAuthor($object);

                    if (! empty($metaAuthor)) {
                        $metaAuthor = ! empty($metaAuthor['url'])
                            ? Link::make(...$metaAuthor)
                            : esc_html($metaAuthor['title']);
                        $args['content']['meta'] = sprintf(__('by %s', 'gust'), $metaAuthor);
                    }
                } elseif ($object->post_type === 'guide') {
                    // Guide (team member): role appears as the meta line.
                    $args['type'] = 'guide';
                    $args['show_read_more'] = false;
                    $args['heading_class'] = 'is-style-type-h4';

                    if ($role = \get_field('role', $object->ID)) {
                        $args['content']['meta'] = esc_html($role);
                    }
                }
            } elseif ($args['object'] instanceof \WP_Term) {
                // ---- Terms: trip style / destination cards ----
                // Image and subheading come from ACF fields on the term.
                $args['content'] = [
                    'heading' => $object->name,
                    'url' => get_term_link($object->term_id),
                    'text' => get_field('subheading', $object) ?: '',
                ];

                if ($image_id = get_field('image', $object)) {
                    $args['content']['image'] = ['ID' => $image_id];
                }
            }

            // Mirror the object URL onto the read-more button (unless caller
            // already provided a different URL for it).
            if (! empty($args['content']['url']) && empty($args['content']['read_more']['url'])) {
                $args['content']['read_more']['url'] = $args['content']['url'];
            }

            // Default the button text to the parent Cards block's choice
            // ("Read More" or "Find Your Trip") when none was set per-card.
            if (empty($args['content']['read_more']['title'])) {
                $args['content']['read_more']['title'] = ! empty($args['read_more_text'])
                    ? $args['read_more_text']
                    : '';
            }
        }
        // ====================================================================
        // BRANCH B — Card built from raw editor content (custom Cards block).
        // Cards.php uses this for source = "Custom Content" (e.g. partnerships).
        // ====================================================================
        elseif (! empty($args['content'])) {
            $content = $args['content'];

            // The ACF "link" field returns ['url'=>..., 'title'=>..., 'target'=>...].
            // We flatten it onto:
            //   - content.url           → wraps the heading
            //   - content.read_more     → the button below the text
            //
            // The link's title field is optional in ACF, and crucially WP's
            // link picker auto-fills it for INTERNAL post selections but leaves
            // it empty for EXTERNAL URLs the editor pastes in. Without the
            // fallback below, that meant external-link partnership cards used
            // to show no button at all.
            if (! empty($content['link'])) {
                $content['url'] = $content['link']['url'];
                $content['read_more']['url'] = $content['link']['url'];
                $content['read_more']['title'] = ! empty($content['link']['title'])
                    ? $content['link']['title']
                    : ($args['read_more_text'] ?? '');

                // "Open in new tab" propagates to both the button and the
                // heading link so the card behaves consistently however the
                // user clicks it.
                if (! empty($content['link']['target'])) {
                    $content['read_more']['target'] = $content['link']['target'];
                    $args['target'] = $content['link']['target'];
                }

                // Belt & braces: if we still ended up with no button text
                // (Cards.php didn't supply a default for some reason), hide it
                // rather than render an empty button.
                if (empty($content['read_more']['title'])) {
                    $args['show_read_more'] = false;
                }
            }

            $args['content'] = $content;
        }

        // ====================================================================
        // STEP 2 — Decorate the read-more button with classes.
        // Runs for both branches above; only fires if there's a button at all.
        // ====================================================================
        if (! empty($args['content']['read_more'])) {
            $read_more_classes = ['btn', 'g-card__find-out-more'];

            // Trip-style cards use the secondary brand button colour.
            if (($args['type'] ?? null) === 'trip-style' || ($args['type'] ?? null) === 'trip-styles') {
                $read_more_classes[] = 'btn--theme-2';
            }

            // Custom cards that came from an editor link field also use the
            // secondary colour — keeps the partnerships page consistent
            // regardless of whether the link is internal or external.
            if (! empty($args['content']['link'])) {
                $read_more_classes[] = 'btn--theme-2';
            }

            // array_merge so caller-provided classes/url/title win.
            $args['content']['read_more'] = array_merge([
                'classes' => $read_more_classes,
            ], $args['content']['read_more']);
        }

        // ====================================================================
        // STEP 3 — Wrapper-level styling: image fit, background, classes.
        // ====================================================================

        // Image fit (cover vs contain) is exposed as a CSS custom property
        // so the styles.pcss can apply it on the image element.
        if (! empty($args['image_fit'])) {
            $args['attributes']['style']['--g-card--image--object-fit'] = $args['image_fit'];
        }

        // Apply WP's standard "has-X-background-color" classes for the chosen
        // card background. 'none' means transparent (used for story/itinerary).
        if (! empty($args['background']) && $args['background'] !== 'none') {
            $args['classes'][] = 'has-'.$args['background'].'-background-color';
            $args['classes'][] = 'has-background';
        }

        // Wrap the heading so the Heading component can render it. From here
        // on `content.heading` is an args array, not a plain string.
        $args['content']['heading'] = [
            'heading' => $args['content']['heading'],
            'classes' => ['g-card__heading'],
        ];

        // If the card has a URL, the heading itself becomes a link (so users
        // can click the title). Target propagates for external links.
        if (! empty($args['content']['url'])) {
            $args['content']['heading']['link'] = $args['content']['url'];

            if (! empty($args['target'])) {
                $args['content']['heading']['target'] = $args['target'];
            }
        }

        // Optional caller-supplied heading style (e.g. is-style-type-h4).
        if (! empty($args['heading_class'])) {
            $args['content']['heading']['classes'][] = $args['heading_class'];
        }

        // ====================================================================
        // STEP 4 — Image sizing.
        // ====================================================================
        if (! empty($args['content']['image'])) {
            // Trip-style cards always use the square crop, even if the caller
            // asked for medium_large — prevents tall images breaking the grid.
            if (($args['type'] ?? null) === 'trip-style' && ($args['image_size'] ?? null) === 'medium_large') {
                $args['image_size'] = 'gust_card_square';
            }

            $args['content']['image']['size'] = $args['image_size'];
        }

        // ====================================================================
        // STEP 5 — Final wrapper classes for CSS hooks.
        // ====================================================================

        // has-image / has-link drive CSS variants (e.g. layout shifts when
        // the card has no image, hover styles when the card is clickable).
        $args['classes'][] = ! empty($args['content']['image']) ? 'has-image' : null;
        $args['classes'][] = ! empty($args['content']['url']) ? 'has-link' : null;

        // Type modifier (g-card--type--article / --story / --guide / etc).
        if (! empty($args['type'])) {
            $args['classes'][] = 'g-card--type--'.$args['type'];
        }

        // If we overwrote the type (e.g. Cards.php said 'horizontal' but
        // the post is a 'story'), keep the original type as an extra class
        // so styles like cards--type--horizontal can still target it.
        if (! empty($incoming_type) && $incoming_type !== ($args['type'] ?? null)) {
            $args['classes'][] = 'g-card--type--'.$incoming_type;
        }

        return $args;
    }
}
