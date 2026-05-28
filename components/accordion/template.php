<section class="<?= classes('accordion', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="accordion__inner content-width-sm align-left w-full">
        <?php if (! empty($this->heading)) { ?>
            <div class="accordion__header">
                <?= \Gust\Components\Heading::make(...$this->heading, el: 'h4'); ?>
            </div>
        <?php } ?>

        <?php if (! empty($this->accordion_items)) { ?>
            <div class="accordion__items">
                <?php foreach ($this->accordion_items as $key => $item) { ?>
                    <div class="accordion__item">
                        <?= \Gust\Components\Button::make(...$item['button']); ?>

                        <div <?= \Gust\Helpers::buildAttributes($item['panel_attributes']); ?>>
                            <div class="accordion__item__panel-inner">
                                <?= wp_kses_post($item['content']) ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>
