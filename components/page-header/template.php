<header class="<?= classes('page-header', 'wp-block', 'alignfull', 'not-prose', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->image) && $this->image_position === 'hero') { ?>
        <div class="page-header__hero-image">
            <div class="page-header__hero-image-inner img-fit">
                <?= $this->image; ?>
            </div>
        </div>
    <?php } ?>

    <div class="page-header__inner">
        <?php if (! empty($this->actions)) { ?>
            <div class="page-header__actions">
                <?php foreach ($this->actions as $action) {
                    echo \Gust\Components\Button::make(
                        content: $action['label'],
                        classes: ['btn', 'btn--theme-2', 'page-header__actions__btn'],
                        attributes: $action['attributes'] ?? [],
                    );
                } ?>
            </div>
        <?php } ?>

        <?php if (! empty($this->image) && $this->image_position === 'square') { ?>
            <div class="page-header__square-image">
                <div class="page-header__square-image-inner img-fit">
                    <?= $this->image; ?>
                </div>
            </div>
        <?php } ?>

        <div class="page-header__content">
            <?php if (! empty($this->back_link)) { ?>
                <a class="page-header__back-link" href="<?= esc_url($this->back_link['url']) ?>">
                    <span class="page-header__back-link__icon" aria-hidden="true"></span>
                    <span class="page-header__back-link__label"><?= esc_html($this->back_link['label']) ?></span>
                </a>
            <?php } elseif ($this->show_breadcrumbs) { ?>
                <?= \Gust\Components\Breadcrumbs::make(); ?>
            <?php } ?>

            <?php if (($this->type ?? '') === 'guide') { ?>
                <?php if (! empty($this->subheading)) { ?>
                    <div class="page-header__subheading">
                        <?= wp_kses_post($this->subheading); ?>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php if (! empty($this->heading)) { ?>
                <?= \Gust\Components\Heading::make(...$this->heading); ?>
            <?php } ?>

            <?php if (($this->type ?? '') !== 'guide') { ?>
                <?php if (! empty($this->subheading)) { ?>
                    <div class="page-header__subheading">
                        <?= wp_kses_post($this->subheading); ?>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php if (! empty($this->meta)) { ?>
                <div class="page-header__meta is-style-type-meta">
                    <?= wp_kses_post($this->meta); ?>
                </div>
            <?php } ?>

            <?php if (! empty($this->labels)) { ?>
                <div class="page-header__labels">
                    <div class="page-header__labels__items flex-list">
                        <?php foreach ($this->labels as $label) {
                            echo \Gust\Components\Link::make(
                                title: $label['name'],
                                url: $label['url'],
                                classes: [
                                    'btn',
                                    'btn--label',
                                ],
                            );
                        } ?>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</header>
