<section id="trip-reviews" class="<?= classes('trip-reviews', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="trip-reviews__inner content-width-lg">
        <?= \Gust\Components\Heading::make(
            heading: __('Reviews', 'gust'),
            classes: ['trip-reviews__heading'],
            el: 'h4',
        ); ?>

        <div class="trip-reviews__embed">
            <?= $this->embed_code; ?>
        </div>
    </div>
</section>
