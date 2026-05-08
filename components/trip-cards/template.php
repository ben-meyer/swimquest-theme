<?php if (! empty($this->items)) { ?>
    <section class="<?= classes(
        'trip-cards',
        'wp-block',
        'animate',
        $this->classes,
    ) ?>" <?= attributes($this->attributes) ?>>
        <div class="trip-cards__inner content-width-lg">
            <?php if (! empty($this->heading) || ! empty($this->subheading)) { ?>
                <div class="trip-cards__header">
                    <?php if (! empty($this->heading)) { ?>
                        <?= \Gust\Components\Heading::make(
                            heading: $this->heading,
                            classes: ['trip-cards__heading', 'type-h1'],
                        ); ?>
                    <?php } ?>

                    <?php if (! empty($this->subheading)) { ?>
                        <div class="trip-cards__subheading">
                            <?= wp_kses_post($this->subheading); ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="trip-cards__items">
                <?php foreach ($this->items as $card) { ?>
                    <?= \Gust\Components\TripCard::make(...$card); ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
