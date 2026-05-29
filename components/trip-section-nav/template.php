<nav class="<?= classes('trip-section-nav', 'wp-block', 'alignfull', $this->classes) ?>" aria-label="<?= esc_attr__('Trip sections', 'gust') ?>" <?= attributes($this->attributes) ?>>
    <div class="trip-section-nav__inner content-width-fluid-lg">
        <ul class="trip-section-nav__items">
            <?php foreach ($this->items as $item) { ?>
                <li>
                    <?= \Gust\Components\Link::make(
                        title: $item['label'],
                        url: $item['url'],
                        classes: ['trip-section-nav__link'],
                    ); ?>
                </li>
            <?php } ?>
        </ul>

        <div class="trip-section-nav__actions">
            <?php if (! empty($this->enquiry_action)) { ?>
                <?= \Gust\Components\Link::make(
                    title: $this->enquiry_action['label'],
                    url: $this->enquiry_action['url'],
                    target: $this->enquiry_action['target'] ?? null,
                    classes: ['btn', 'btn--theme-2'],
                ); ?>
            <?php } ?>

            <?php if (! empty($this->booking_action)) { ?>
                <?php if (! empty($this->booking_action['is_link'])) { ?>
                    <?= \Gust\Components\Link::make(
                        title: $this->booking_action['label'],
                        url: $this->booking_action['url'],
                        target: $this->booking_action['target'] ?? null,
                        classes: ['btn', 'trip-section-nav__booking-btn'],
                    ); ?>
                <?php } else { ?>
                    <span class="btn trip-section-nav__booking-btn trip-section-nav__status"><?= esc_html($this->booking_action['label']); ?></span>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</nav>
