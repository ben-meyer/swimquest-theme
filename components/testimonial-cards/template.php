<?php if (! empty($this->items)) { ?>
    <section class="<?= classes('testimonial-cards', 'wp-block', 'animate', $this->classes) ?>" <?= attributes($this->attributes) ?>>
        <div class="testimonial-cards__inner content-width-lg">
            <?php if (! empty($this->heading) || ! empty($this->subheading)) { ?>
                <div class="testimonial-cards__header">
                    <?php if (! empty($this->heading)) { ?>
                        <?= \Gust\Components\Heading::make(
                            heading: $this->heading,
                            classes: ['testimonial-cards__heading', 'type-h1',],
                        ); ?>
                    <?php } ?>

                    <?php if (! empty($this->subheading)) { ?>
                        <div class="testimonial-cards__subheading">
                            <?= wp_kses_post($this->subheading); ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="testimonial-cards__items">
                <?php foreach ($this->items as $item) { ?>
                    <?= \Gust\Components\TestimonialCard::make(...$item); ?>
                <?php } ?>
            </div>

            <?php if (! empty($this->button)) { ?>
                <div class="testimonial-cards__footer">
                    <?= \Gust\Components\Link::make(...$this->button); ?>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>
