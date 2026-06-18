<?php

namespace Theme\Modules\Core;

class Menus
{
    private const RESTRICTED_THEME_SCREENS = [
        'themes.php',
        'customize.php',
        'widgets.php',
        'theme-editor.php',
    ];

    public static function init()
    {
        \add_filter('after_setup_theme', [__CLASS__, 'registerThemeMenus']);
        \add_action('admin_init', [__CLASS__, 'grantEditorsMenuAccess']);
        \add_action('admin_menu', [__CLASS__, 'hideAppearanceMenuForNonAdmins'], 999);
        \add_action('admin_init', [__CLASS__, 'blockThemeScreensForNonAdmins']);
        \add_action('admin_bar_menu', [__CLASS__, 'removeCustomizeFromAdminBar'], 999);
    }

    public static function registerThemeMenus(): void
    {
        \register_nav_menus([
            'header' => _x('Header', 'Menu name', 'gust'),
            'footer-1' => _x('Footer 1', 'Menu name', 'gust'),
            'footer-2' => _x('Footer 2', 'Menu name', 'gust'),
        ]);
    }

    public static function grantEditorsMenuAccess(): void
    {
        $editor = \get_role('editor');

        if ($editor && ! $editor->has_cap('edit_theme_options')) {
            $editor->add_cap('edit_theme_options');
        }
    }

    public static function hideAppearanceMenuForNonAdmins(): void
    {
        if (\current_user_can('manage_options')) {
            return;
        }

        \remove_menu_page('themes.php');
    }

    public static function blockThemeScreensForNonAdmins(): void
    {
        if (\current_user_can('manage_options')) {
            return;
        }

        global $pagenow;

        if (in_array($pagenow, self::RESTRICTED_THEME_SCREENS, true)) {
            \wp_safe_redirect(\admin_url('nav-menus.php'));
            exit;
        }
    }

    public static function removeCustomizeFromAdminBar(\WP_Admin_Bar $bar): void
    {
        if (\current_user_can('manage_options')) {
            return;
        }

        $bar->remove_node('customize');
    }
}
