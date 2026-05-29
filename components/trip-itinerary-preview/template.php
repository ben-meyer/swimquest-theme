<section id="trip-itinerary" class="<?= classes('trip-itinerary-preview', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="trip-itinerary-preview__inner content-width-lg align-left">
        <?= \Gust\Components\Heading::make(
            heading: __('Itinerary', 'gust'),
            classes: ['trip-itinerary-preview__heading'],
            el: 'h4',
        ); ?>

        <?php if (! empty($this->items)) { ?>
            <div class="trip-itinerary-preview__items content-width-sm align-left">
                <?php foreach ($this->items as $item) { ?>
                    <article class="trip-itinerary-preview__item">
                        <div class="trip-itinerary-preview__item-content">
                            <div class="trip-itinerary-preview__day-label"><?= esc_html(sprintf(__('Day %d', 'gust'), $item['day'])); ?></div>

                            <?= \Gust\Components\Heading::make(
                                heading: $item['title'],
                                el: 'h3',
                                classes: ['trip-itinerary-preview__title'],
                            ); ?>

                            <div class="trip-itinerary-preview__summary"><?= wp_kses_post($item['summary']); ?></div>
                        </div>
                    </article>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="trip-itinerary-preview__fallback content-width-md align-left">
                <?php if (! empty($this->title)) { ?>
                    <?= \Gust\Components\Heading::make(
                        heading: $this->title,
                        el: 'h3',
                        classes: ['trip-itinerary-preview__title'],
                    ); ?>
                <?php } ?>

                <?php if (! empty($this->preview)) { ?>
                    <div class="trip-itinerary-preview__summary"><?= esc_html($this->preview); ?></div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (! empty($this->url)) { ?>
            <?= \Gust\Components\Link::make(
                title: __('View full itinerary', 'gust'),
                url: $this->url,
                classes: ['btn', 'btn--theme-2'],
                target: '_blank',
            ); ?>
        <?php } ?>
    </div>
</section>
