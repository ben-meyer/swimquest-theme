<?php

namespace Theme\Modules\Stories;

class PostType
{
    protected const SLUG = 'story';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'register']);
        \add_filter('gust/templates/post-types', [__CLASS__, 'filterGustTemplatesPostTypes']);
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
            'menu_position' => 15,
            'menu_icon' => 'dashicons-format-quote',
            'enter_title_here' => 'Story Title',
            'supports' => [
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'revisions',
                'custom-fields',
            ],
            'admin_cols' => [
                'thumbnail' => [
                    'title' => 'Thumbnail',
                    'featured_image' => 'thumbnail',
                    'width' => 80,
                    'height' => 80,
                ],
                'title' => ['title' => 'Title'],
                'updated' => [
                    'title' => 'Updated',
                    'post_field' => 'post_modified',
                    'date_format' => 'Y/m/d',
                ],
            ],
        ], [
            'singular' => __('Story', 'gust'),
            'plural' => __('Stories', 'gust'),
            'slug' => 'news-and-stories',
        ]);
    }

    public static function filterGustTemplatesPostTypes(array $postTypes): array
    {
        $postTypes[] = self::SLUG;

        return $postTypes;
    }
}
