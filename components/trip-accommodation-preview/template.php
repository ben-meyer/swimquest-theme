<section id="trip-accommodation" class="<?= classes('trip-accommodation-preview', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="trip-accommodation-preview__inner content-width-lg">
        <?= \Gust\Components\Heading::make(
            heading: __('Accommodation', 'gust'),
            el: 'h4',
            classes: ['trip-accommodation-preview__heading'],
        ); ?>

        <div class="trip-accommodation-preview__summary content-width-sm align-left">
            <div class="trip-accommodation-preview__header">
                <?php if (! empty($this->title)) { ?>
                    <?= \Gust\Components\Heading::make(
                        heading: $this->title,
                        el: 'h5',
                        classes: ['trip-accommodation-preview__title'],
                        link: $this->url ?: null,
                    ); ?>
                <?php } ?>

                <?php if ($this->star_rating) { ?>
                    <?= \Gust\Components\Stars::make(
                        stars: $this->star_rating,
                        classes: ['trip-accommodation-preview__star-rating'],
                    ); ?>
                <?php } ?>
            </div>

            <?php if (! empty($this->tags)) { ?>
                <ul class="trip-accommodation-preview__tags">
                    <?= \Gust\Components\Tags::make(
                        tags: $this->tags,
                        classes: ['trip-accommodation-preview__tag-list'],
                    ); ?>
                </ul>
            <?php } ?>

            <?php if (! empty($this->description)) { ?>
                <div class="trip-accommodation-preview__description"><?= wp_kses_post($this->description); ?></div>
            <?php } ?>

            <?php if (! empty($this->gallery)) { ?>
                <div class="trip-accommodation-preview__gallery">
                    <?php foreach ($this->gallery as $image) { ?>
                        <div class="trip-accommodation-preview__gallery-item">
                            <div class="trip-accommodation-preview__gallery-item-inner img-fit">
                                <?= $image; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

</div>

        <?php if (! empty($this->rooms_intro) || ! empty($this->url)) { ?>
            <div class="trip-accommodation-preview__rooms content-width-sm align-left">
                <?php if (! empty($this->rooms_intro)) { ?>
                    <div class="trip-accommodation-preview__rooms-copy">
                        <?= \Gust\Components\Heading::make(
                            heading: __('Rooms', 'gust'),
                            el: 'h6',
                            classes: ['trip-accommodation-preview__rooms-heading', 'color-mid-blue'],
                        ); ?>
                        <div><?= wp_kses_post($this->rooms_intro); ?></div>
                    </div>
                <?php } ?>

                <?php if (! empty($this->url)) { ?>
                    <?= \Gust\Components\Link::make(
                        title: __('View accommodation', 'gust'),
                        url: $this->url,
                        classes: ['btn', 'btn--theme-2'],
                        target: '_blank',
                    ); ?>
                <?php } ?>
            </div>
        <?php } ?>
</section>
