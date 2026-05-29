<?php

namespace Theme\Modules\Events;

class PostType
{
    protected const SLUG = 'events';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'register']);
        \add_filter('gust/templates/post-types', [__CLASS__, 'filterGustTemplatesPostTypes']);
        \add_filter('use_block_editor_for_post_type', [__CLASS__, 'disableBlockEditor'], 10, 2);
        \add_filter('gutenberg_can_edit_post_type', [__CLASS__, 'disableBlockEditor'], 10, 2);
    }

    public static function register(): void
    {
        if (! function_exists('register_extended_post_type')) {
            return;
        }

        \register_extended_post_type(self::SLUG, [
            'public' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-calendar-alt',
            'enter_title_here' => 'Event Name',
            'supports' => [
                'title',
                'excerpt',
                'thumbnail',
                'revisions',
                'custom-fields',
                'slug',
            ],
            'taxonomies' => [
                'trip_style',
                'skill_level',
                'swim_type',
                'country',
                'location',
            ],
            'admin_filters' => [
                'trip_style' => ['taxonomy' => 'trip_style'],
                'country' => ['taxonomy' => 'country'],
                'swim_type' => ['taxonomy' => 'swim_type'],
            ],
            'admin_cols' => [
                'thumbnail' => [
                    'title' => 'Thumbnail',
                    'featured_image' => 'thumbnail',
                    'width' => 80,
                    'height' => 80,
                ],
                'title' => ['title' => 'Title'],
                'country' => ['taxonomy' => 'country'],
                'location' => ['taxonomy' => 'location'],
                'trip_style' => ['taxonomy' => 'trip_style'],
                'swim_type' => ['taxonomy' => 'swim_type'],
                'skill_level' => ['taxonomy' => 'skill_level'],
                'updated' => [
                    'title' => 'Updated',
                    'post_field' => 'post_modified',
                    'date_format' => 'Y/m/d',
                ],
            ],
        ], [
            'singular' => __('Event', 'gust'),
            'plural' => __('Events', 'gust'),
            'slug' => self::SLUG,
        ]);
    }

    public static function filterGustTemplatesPostTypes(array $postTypes): array
    {
        $postTypes[] = self::SLUG;

        return $postTypes;
    }

    public static function disableBlockEditor(bool $canEdit, string $postType): bool
    {
        if ($postType === self::SLUG) {
            return false;
        }

        return $canEdit;
    }
}
