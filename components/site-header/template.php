<header class="<?= classes('site-header', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-header__bar_wrapper">
        <div class="site-header__bar">
        <?= \Gust\Components\Link::make(
            url: home_url('/'),
            classes: ['site-header__logo', 'img-fit'],
            title: '<span class="logo-default">' . \Gust\Image::get('logo-alt.svg', [
                'alt' => get_bloginfo('name'),
                'loading' => false,
                'attributes' => [
                    'data-spai-eager' => class_exists('\\ShortPixelAI') ? 'true' : null,
                ],
            ]) . '</span>' .
            '<span class="logo-white" aria-hidden="true">' . \Gust\Image::get('logo-white.svg', [
                'alt' => '',
                'loading' => false,
            ]) . '</span>',
            content_filter: '',
        ); ?>

        <nav class="site-header__nav">
            <?= \Gust\Components\Menu::make(
                theme_location: 'header',
                menu_id: 'main-menu-desktop',
                classes: ['site-header__navigation'],
            ); ?>

            <?php if (! empty($this->content['call_to_action_1'])) { ?>
                <?= \Gust\Components\Link::make(...$this->content['call_to_action_1']); ?>
            <?php } ?>
        </nav>

        <div class="site-header__buttons">
            <button
                class="site-header__burger js-site-header-toggle btn btn--ghost"
                aria-controls="site-header-menu-panel"
                aria-expanded="false"
                type="button">
                <?= \Gust\Components\Burger::make(); ?>
                <span class="sr-only js-site-header-toggle-label"><?= __('Open main menu', 'gust') ?></span>
                <span aria-hidden="true" data-show-collapsed>Menu</span>
                <span aria-hidden="true" data-hide-collapsed>Close</span>
            </button>
        </div>
        </div>
    </div>

    <div class="site-header__panel site-header__menu-panel" id="site-header-menu-panel" inert>
        <div class="site-header__panel-content">
            <?= \Gust\Components\Menu::make(
                theme_location: 'header',
                menu_id: 'main-menu',
                classes: ['site-header__navigation'],
            ); ?>

            <?php if (! empty($this->content['call_to_action_1'])) { ?>
                <?= \Gust\Components\Link::make(...$this->content['call_to_action_1']); ?>
            <?php } ?>
        </div>
    </div>
</header>
