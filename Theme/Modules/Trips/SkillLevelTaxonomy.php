<?php

namespace Theme\Modules\Trips;

class SkillLevelTaxonomy
{
    protected const SLUG = 'skill_level';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'register']);
        \add_action('admin_enqueue_scripts', [__CLASS__, 'removeNativeDescriptionField']);
    }

    public static function register(): void
    {
        if (! function_exists('register_extended_taxonomy')) {
            return;
        }

        \register_extended_taxonomy(
            self::SLUG,
            ['trip', 'events'],
            [
                'hierarchical' => false,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'meta_box' => 'simple',
                'public' => false,
                'rewrite' => false,
            ],
            [
                'singular' => __('Skill Level', 'gust'),
                'plural' => __('Skill Levels', 'gust'),
                'slug' => 'skill-level',
            ]
        );
    }

    public static function removeNativeDescriptionField(): void
    {
        $screen = \get_current_screen();
        if (! $screen || $screen->taxonomy !== self::SLUG) {
            return;
        }

        \wp_add_inline_script('wp-util', '
            document.addEventListener("DOMContentLoaded", function () {
                var el = document.querySelector(".term-description-wrap");
                if (el) el.parentNode.removeChild(el);
            });
        ');
    }
}
