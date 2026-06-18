<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * PageHeader Component
 *
 * Usage:
 *   use Gust\Components\PageHeader;
 *
 *   echo PageHeader::make();
 */
class PageHeader extends ComponentBase
{
    protected static string $name = 'page-header';

    protected static function getDefaults(): array
    {
        return [
            'image_position' => 'hero',
            'background' => 'accent',
            'show_breadcrumbs' => true,
        ];
    }

    /**
     * Create a new PageHeader component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?object $object = null,
        array $classes = [],
        ?string $type = null,
        ?string $image_position = null,
        ?string $background = null,
        array $attributes = [],
        ?bool $show_breadcrumbs = null,
        ?array $back_link = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        if (isset($args['is_preview']) && $args['is_preview']) {
            $args['object'] = \get_post($args['post_id']);
        } elseif (empty($args['object'])) {
            $args['object'] = \Gust\WordPress\PageObject::get() ?? null;
        }

        $is_guide = ! empty($args['type']) && $args['type'] === 'guide';

        $heading = '';

        if (! empty($args['object'])) {
            $object = $args['object'];

            if ($object instanceof \WP_Term) {
                $heading = $object->name;
                $args['show_breadcrumbs'] = false;

                if ($subheading = \get_field('subheading', $object)) {
                    $args['subheading'] = $subheading;
                }

                if ($object->taxonomy === 'trip_style') {
                    $args['image'] = null;
                }
            } elseif ($object instanceof \WP_Post_Type) {
                if ($routerPage = \Gust\Router::getPage()) {
                    $object = $routerPage;
                } else {
                    $heading = $object->label;
                }
            } elseif ($object instanceof \WP_Query && $object->is_404()) {
                $heading = __('404', 'gust');
            } elseif ($object instanceof \WP_Query && $object->is_search()) {
                $heading = __('Search', 'gust');
            } elseif ($object instanceof \WP_User) {
                $heading = sprintf(__('Posts by %s', 'gust'), $object->data->display_name);
            }

            if ($object instanceof \WP_Post) {
                $heading = $object->post_title;

                if ($object->post_type === 'post') {
                    $args['image'] = \get_post_thumbnail_id($object);
                    $args['meta'] = sprintf(__('Published on %s ', 'gust'), \get_the_date(\get_option('date_format'), $object->ID));
                    $args['labels'] = \Theme\Utils\ObjectMeta::getObjectLabels($object->ID, ['limit' => 3, 'taxonomies' => ['category']]);
                    $args['background'] = false;
                    $args['type'] = 'article';

                    if ($author_name = \get_the_author_meta('display_name', $object->post_author)) {
                        $args['meta'] .= sprintf(__('by %s ', 'gust'), $author_name);
                    }
                } elseif ($object->post_type === 'page') {
                    if (\is_front_page()) {
                        $args['classes'][] = 'page-header--home';
                        $args['show_breadcrumbs'] = false;
                    }

                    if (empty($object->post_parent)) {
                        $args['show_breadcrumbs'] = false;
                    }

                    // Remove breadcrumbs if no image is present on child pages
                    if (! empty($object->post_parent) && empty($args['image'])) {
                        $args['show_breadcrumbs'] = false;
                    }
                } elseif ($object->post_type === 'story') {
                    $args['background'] = 'none';
                    $args['image'] = null;
                    $args['type'] = 'story';
                    $args['classes'][] = 'page-header--align-left';

                    if ($contributor = \get_field('contributor_name', $object->ID)) {
                        $args['meta'] = sprintf(__('By %s', 'gust'), $contributor);
                    }
                } elseif ($object->post_type === 'guide') {
                    $is_guide = true;

                    if (empty($args['image']) && ($thumbnail_id = \get_post_thumbnail_id($object))) {
                        $args['image'] = $thumbnail_id;
                        $args['image_position'] = 'square';
                    }
                } elseif (in_array($object->post_type, ['accommodation', 'itinerary'], true)) {
                    $args['background'] = 'none';
                    $args['image'] = null;
                    $args['classes'][] = 'page-header--align-left';
                    $args['type'] = $object->post_type;
                    $args['show_breadcrumbs'] = false;
                }

                if ($heading === 'Auto Draft') {
                    $heading = __('Post Title', 'gust');
                }

                unset($args['object']);
            }
        }

        $args['heading'] = $args['heading'] ?? $heading;

        if (! empty($heading) && empty($args['heading'])) {
            $args['heading'] = $heading;
        }

        if (isset($args['is_preview']) && $args['is_preview']) {
            if (empty($args['heading'])) {
                $args['heading'] = _x('Add title', 'Placeholder for page header title', 'gust');
            }

            if (empty($args['subheading'])) {
                $args['subheading'] = _x('Add subheading', 'Placeholder for page header subheading', 'gust');
            }
        }

        if (! empty($args['image'])) {
            if (is_array($args['image'])) {
                $args['image'] = $args['image']['ID'];
            }

            if (($args['image_position'] ?? '') === 'square') {
                $args['image'] = Image::make(
                    id: $args['image'],
                    size: 'gust_card_square',
                    sizes: '300px',
                );
                $args['classes'][] = 'has-square-image';
            } else {
                $args['image'] = Image::make(
                    id: $args['image'],
                    size: 'gust_super',
                    sizes: '100vw',
                );
                $args['classes'][] = 'has-hero-image';
            }
        }

        if (! empty($args['heading'])) {
            $args['heading'] = [
                'heading' => $args['heading'],
                'el' => 'h1',
                'classes' => ['page-header__heading', 'is-style-type-h1'],
            ];
        }

        if ($is_guide) {
            $args['background'] = 'none';
            $args['show_breadcrumbs'] = false;
            $args['subheading'] = __('Meet our team', 'gust');
            $args['type'] = 'guide';
        }

        if (! empty($args['background']) && $args['background'] !== 'none') {
            $args['classes'][] = 'has-'.$args['background'].'-background-color';
            $args['classes'][] = 'has-background';
        }

        if (! empty($args['type'])) {
            $args['classes'][] = 'page-header--type--'.$args['type'];
        }

        if (! empty($args['image_overlay_opacity'])) {
            $args['attributes']['style']['--page-header--overlay-opacity'] = $args['image_overlay_opacity'].'%';
        }

        if (! empty($args['show_breadcrumbs'])) {
            $args['classes'][] = 'has-breadcrumbs';
        }

        return $args;
    }
}
