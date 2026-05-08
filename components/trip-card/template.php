<article class="<?= classes('trip-card', 'animate-element', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->content['image'])) { ?>
        <div class="trip-card__image">
            <div class="trip-card__image-inner img-fit">
                <?= \Gust\Components\Image::make(...$this->content['image']); ?>
            </div>
        </div>
    <?php } ?>

    <div class="trip-card__inner">
        <?php if (! empty($this->content['heading'])) { ?>
            <?= \Gust\Components\Heading::make(...$this->content['heading']); ?>
        <?php } ?>

        <?php if (! empty($this->content['meta'])) { ?>
            <ul class="trip-card__meta">
                <?php foreach ($this->content['meta'] as $item) { ?>
                    <li class="trip-card__meta-item">
                        <span class="trip-card__meta-icon">
                            <?= \Gust\SVG::get($item['icon']); ?>
                        </span>
                        <span class="trip-card__meta-text"><?= wp_kses_post($item['html']); ?></span>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>

        <?php if (! empty($this->content['price'])) { ?>
            <div class="trip-card__price">
                <?= nl2br(esc_html($this->content['price'])); ?>
            </div>
        <?php } ?>

        <?php if (! empty($this->content['url'])) { ?>
            <?= \Gust\Components\Link::make(
                title: __('View Trip & Book', 'gust'),
                url: $this->content['url'],
                classes: ['btn', 'trip-card__cta'],
            ); ?>
        <?php } ?>
    </div>
</article>
