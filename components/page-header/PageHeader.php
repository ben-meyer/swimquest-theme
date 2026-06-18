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
        ?array $actions = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        // ====================================================================
        // STEP 1 — Work out which WP object this page header represents.
        //
        // The header is normally rendered from theme templates which pass
        // nothing in — we resolve the object from the current request via
        // PageObject::get(). The ACF preview in the editor passes is_preview
        // + post_id so we can render in the block panel. Callers can also
        // pass a concrete $object in programmatically (e.g. taxonomy archives).
        // ====================================================================
        if (isset($args['is_preview']) && $args['is_preview']) {
            // Block editor preview: load the post being edited.
            $args['object'] = \get_post($args['post_id']);
        } elseif (empty($args['object'])) {
            // Front-end: resolve the current page/term/post-type/etc.
            $args['object'] = \Gust\WordPress\PageObject::get() ?? null;
        }

        // Guide pages need special treatment all the way through this method;
        // track it once so we don't keep re-checking the type string.
        // The flag is set initially from a caller-supplied type, and also
        // gets flipped on later when we detect a guide post.
        $is_guide = ! empty($args['type']) && $args['type'] === 'guide';

        $heading = '';

        if (! empty($args['object'])) {
            $object = $args['object'];

            // ----------------------------------------------------------------
            // Taxonomy archive (category/trip_style/country/etc.)
            // ----------------------------------------------------------------
            if ($object instanceof \WP_Term) {
                $heading = $object->name;
                // Breadcrumbs aren't useful on term archives — the term name
                // is itself the navigational context.
                $args['show_breadcrumbs'] = false;

                if ($subheading = \get_field('subheading', $object)) {
                    $args['subheading'] = $subheading;
                }

                // Trip-style archives have their own styled hero in the
                // template; suppress the page-header background image so it
                // doesn't double up.
                if ($object->taxonomy === 'trip_style') {
                    $args['image'] = null;
                }
            }
            // ----------------------------------------------------------------
            // Post-type archive (/stories, /events, etc.)
            // If the router has a real Page assigned to this archive
            // (e.g. an editor-managed "Stories" page), prefer that page's
            // content. Otherwise just use the post type label.
            // ----------------------------------------------------------------
            elseif ($object instanceof \WP_Post_Type) {
                if ($routerPage = \Gust\Router::getPage()) {
                    $object = $routerPage;
                } else {
                    $heading = $object->label;
                }
            }
            // ----------------------------------------------------------------
            // Special pages with no underlying post: 404, search, author.
            // ----------------------------------------------------------------
            elseif ($object instanceof \WP_Query && $object->is_404()) {
                $heading = __('404', 'gust');
            } elseif ($object instanceof \WP_Query && $object->is_search()) {
                $heading = __('Search', 'gust');
            } elseif ($object instanceof \WP_User) {
                $heading = sprintf(__('Posts by %s', 'gust'), $object->data->display_name);
            }

            // ----------------------------------------------------------------
            // Concrete post (page, post, story, guide, accommodation, itinerary).
            //
            // Note: this is NOT an `elseif` — the post-type-archive branch
            // above may reassign $object to a WP_Post (the router page), in
            // which case we want to fall through into here and treat it as a
            // normal post.
            // ----------------------------------------------------------------
            if ($object instanceof \WP_Post) {
                $heading = $object->post_title;

                // ---- News article ----------------------------------------
                // Hero image + dated/authored byline + category labels.
                if ($object->post_type === 'post') {
                    $args['image'] = \get_post_thumbnail_id($object);
                    $args['meta'] = sprintf(__('Published on %s ', 'gust'), \get_the_date(\get_option('date_format'), $object->ID));
                    $args['labels'] = \Theme\Utils\ObjectMeta::getObjectLabels($object->ID, ['limit' => 3, 'taxonomies' => ['category']]);
                    $args['background'] = false;
                    $args['type'] = 'article';

                    if ($author_name = \get_the_author_meta('display_name', $object->post_author)) {
                        $args['meta'] .= sprintf(__('by %s ', 'gust'), $author_name);
                    }
                }
                // ---- Page ------------------------------------------------
                // Breadcrumbs are only useful when the page is nested AND
                // has a hero image to anchor them against.
                elseif ($object->post_type === 'page') {
                    // Home page: distinct visual treatment, no breadcrumbs.
                    if (\is_front_page()) {
                        $args['classes'][] = 'page-header--home';
                        $args['show_breadcrumbs'] = false;
                    }

                    // Top-level pages have nothing to breadcrumb back to.
                    if (empty($object->post_parent)) {
                        $args['show_breadcrumbs'] = false;
                    }

                    // Child pages without a hero image: breadcrumbs hang in
                    // empty space and look wrong, so hide them.
                    if (! empty($object->post_parent) && empty($args['image'])) {
                        $args['show_breadcrumbs'] = false;
                    }
                }
                // ---- Story (user-submitted blog) -------------------------
                // No hero image, no background colour — story singles use a
                // left-aligned editorial header.
                elseif ($object->post_type === 'story') {
                    $args['background'] = 'none';
                    $args['image'] = null;
                    $args['type'] = 'story';
                    $args['classes'][] = 'page-header--align-left';

                    if ($contributor = \get_field('contributor_name', $object->ID)) {
                        $args['meta'] = sprintf(__('By %s', 'gust'), $contributor);
                    }
                }
                // ---- Guide (team-member single) --------------------------
                // Always rendered in the "guide" style further down. The
                // featured image is shown as a 300px square next to the
                // heading rather than as a full-bleed hero.
                elseif ($object->post_type === 'guide') {
                    $is_guide = true;

                    if (empty($args['image']) && ($thumbnail_id = \get_post_thumbnail_id($object))) {
                        $args['image'] = $thumbnail_id;
                        $args['image_position'] = 'square';
                    }

                    if ($role = \get_field('role', $object->ID)) {
                        $args['meta'] = esc_html($role);
                    }
                }
                // ---- Accommodation / Itinerary ---------------------------
                // Sub-pages within a trip's content. Use a stripped-back
                // left-aligned header (no background, no hero, no
                // breadcrumbs — these aren't standalone pages, they're
                // sections of the trip page).
                elseif (in_array($object->post_type, ['accommodation', 'itinerary'], true)) {
                    $args['background'] = 'none';
                    $args['image'] = null;
                    $args['classes'][] = 'page-header--align-left';
                    $args['type'] = $object->post_type;
                    $args['show_breadcrumbs'] = false;

                    // Itineraries get a "Download & Print" button in the
                    // header toolbar (window.print() — no PDF generation).
                    if ($object->post_type === 'itinerary') {
                        $args['actions'][] = [
                            'label' => __('Download & Print', 'gust'),
                            'attributes' => ['onclick' => 'window.print()', 'type' => 'button'],
                        ];
                    }
                }

                // Brand-new draft posts have the placeholder title
                // "Auto Draft" until the user types one — show a friendlier
                // placeholder in the editor preview instead.
                if ($heading === 'Auto Draft') {
                    $heading = __('Post Title', 'gust');
                }

                // The object isn't needed in the template — drop it so it
                // doesn't get passed through to the rendered HTML attributes.
                unset($args['object']);
            }
        }

        // Honour an explicit heading override from the caller; otherwise use
        // whatever we computed from the object above. The double check below
        // is a safety net — older callers used to write `heading` as null and
        // expect the computed value to win.
        $args['heading'] = $args['heading'] ?? $heading;

        if (! empty($heading) && empty($args['heading'])) {
            $args['heading'] = $heading;
        }

        // Block editor preview: show placeholder text so the editor can see
        // where the heading and subheading will appear before they type.
        if (isset($args['is_preview']) && $args['is_preview']) {
            if (empty($args['heading'])) {
                $args['heading'] = _x('Add title', 'Placeholder for page header title', 'gust');
            }

            if (empty($args['subheading'])) {
                $args['subheading'] = _x('Add subheading', 'Placeholder for page header subheading', 'gust');
            }
        }

        // ====================================================================
        // STEP 2 — Image rendering.
        //
        // Two layouts:
        //   - "square" position → small 300px image next to heading (guides)
        //   - default (hero)    → full-bleed 100vw image behind heading
        // ====================================================================
        if (! empty($args['image'])) {
            // Caller might pass an ACF image array or just the attachment ID.
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

        // Wrap the heading string as a Heading component args array. From
        // here on `heading` is structured data, not a plain string.
        if (! empty($args['heading'])) {
            $args['heading'] = [
                'heading' => $args['heading'],
                'el' => 'h1',
                'classes' => ['page-header__heading', 'is-style-type-h1'],
            ];
        }

        // Guide pages get their styling applied last so it can't be undone
        // by an earlier branch. Forces the type, kills the background and
        // breadcrumbs, and stamps the shared "Meet our team" subheading.
        if ($is_guide) {
            $args['background'] = 'none';
            $args['show_breadcrumbs'] = false;
            $args['subheading'] = __('Meet our team', 'gust');
            $args['type'] = 'guide';
        }

        // ====================================================================
        // STEP 3 — Final wrapper classes.
        // ====================================================================

        // Standard WP "has-X-background-color" pair. 'none' explicitly opts
        // out (used by stories, accommodation, itineraries, guides).
        if (! empty($args['background']) && $args['background'] !== 'none') {
            $args['classes'][] = 'has-'.$args['background'].'-background-color';
            $args['classes'][] = 'has-background';
        }

        // Type modifier (page-header--type--article / --story / --guide / etc).
        if (! empty($args['type'])) {
            $args['classes'][] = 'page-header--type--'.$args['type'];
        }

        // Editor-set overlay darkness on hero images, exposed as a CSS
        // custom property so styles.pcss can apply it to the overlay layer.
        if (! empty($args['image_overlay_opacity'])) {
            $args['attributes']['style']['--page-header--overlay-opacity'] = $args['image_overlay_opacity'].'%';
        }

        // Class hook so styles can reserve space for the breadcrumb bar
        // (the breadcrumbs themselves render in the template).
        if (! empty($args['show_breadcrumbs'])) {
            $args['classes'][] = 'has-breadcrumbs';
        }

        return $args;
    }
}
