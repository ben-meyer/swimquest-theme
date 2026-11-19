<?php

namespace Theme\Modules\Redirection;

class RedirectionModule
{
    public static function init(): void
    {
        // Allow editors to manage the Redirection plugin (default: manage_options)
        // https://redirection.me/developer/wordpress-hooks/
        \add_filter('redirection_role', [__CLASS__, 'role']);
    }

    public static function role(string $role): string
    {
        return defined('REDIRECTION_VERSION') ? 'edit_others_posts' : $role;
    }
}
