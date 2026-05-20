<?php

namespace Theme\Modules\Core;

class Admin
{
    public static function init(): void
    {
        \add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueAdminBarStyles']);
        \add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueAdminBarStyles']);
        \add_filter('allowed_block_types_all', [__CLASS__, 'restrictHomepageBlocks'], 10, 2);
    }

    public static function restrictHomepageBlocks(bool|array $allowedBlocks, \WP_Block_Editor_Context $context): bool|array
    {
        if (! isset($context->post)) {
            return $allowedBlocks;
        }

        $frontPageId = (int) \get_option('page_on_front');
        $isHomepage = $context->post->ID === $frontPageId;
        $blocksToExclude = $isHomepage
            ? ['acf/page-header']
            : ['acf/homepage-hero-header'];

        if ($allowedBlocks === true) {
            $allBlocks = array_keys(\WP_Block_Type_Registry::get_instance()->get_all_registered());
            return array_values(array_filter($allBlocks, fn($block) => ! in_array($block, $blocksToExclude, true)));
        }

        return array_values(array_filter($allowedBlocks, fn($block) => ! in_array($block, $blocksToExclude, true)));
    }

    public static function enqueueAdminBarStyles(): void
    {
        if (! \is_admin_bar_showing()) {
            return;
        }

        \Gust\Vite::enqueueStyle('gust-admin-bar-styles', 'components/admin/admin-bar.pcss');
    }
}
