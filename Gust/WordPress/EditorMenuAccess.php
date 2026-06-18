<?php

namespace Gust\WordPress;

/**
 * Opt-in: let lower-privileged roles (e.g. editors) manage nav menus
 * without exposing the rest of the Appearance surface.
 *
 * WordPress gates nav-menus.php behind `edit_theme_options`, which also
 * unlocks Themes, Customize, Widgets, and the theme file editor. Granting
 * the cap alone therefore overshares.
 *
 * Calling EditorMenuAccess::enable() from theme setup:
 *
 *  - grants `edit_theme_options` to the configured role(s)
 *  - lowers the cap on Gust's top-level "Menus" item to `edit_theme_options`
 *  - hides the Appearance top-level menu for non-admins
 *  - redirects direct URLs to themes.php / customize.php / widgets.php /
 *    theme-editor.php away to nav-menus.php for non-admins
 *  - removes the admin-bar Customize node for non-admins
 *
 * Admins (`manage_options`) are unaffected.
 *
 * Usage (e.g. in functions.php or a theme module):
 *
 *  \Gust\WordPress\EditorMenuAccess::enable();          // default: 'editor'
 *  \Gust\WordPress\EditorMenuAccess::enable(['editor', 'shop_manager']);
 */
class EditorMenuAccess
{
    private const RESTRICTED_THEME_SCREENS = [
        'themes.php',
        'customize.php',
        'widgets.php',
        'theme-editor.php',
    ];

    /**
     * @var string[] Roles that should receive nav-menu access.
     */
    private static array $roles = [];

    private static bool $enabled = false;

    /**
     * @param  string[]  $roles  Role slugs to grant nav-menu access to.
     */
    public static function enable(array $roles = ['editor']): void
    {
        if (self::$enabled) {
            return;
        }

        self::$enabled = true;
        self::$roles = $roles;

        \add_filter('gust/menus_top_level_cap', [__CLASS__, 'filterTopLevelMenusCap']);
        \add_action('admin_init', [__CLASS__, 'grantNavMenuCap']);
        \add_action('admin_init', [__CLASS__, 'blockThemeScreensForNonAdmins']);
        \add_action('admin_menu', [__CLASS__, 'hideAppearanceMenuForNonAdmins'], 999);
        \add_action('admin_bar_menu', [__CLASS__, 'removeCustomizeFromAdminBar'], 999);
    }

    public static function filterTopLevelMenusCap(string $cap): string
    {
        return 'edit_theme_options';
    }

    public static function grantNavMenuCap(): void
    {
        foreach (self::$roles as $role_slug) {
            $role = \get_role($role_slug);

            if ($role && ! $role->has_cap('edit_theme_options')) {
                $role->add_cap('edit_theme_options');
            }
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
