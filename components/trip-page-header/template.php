<?php
use Gust\SVG;
?>

<section class="<?= classes('trip-page-header', 'wp-block', 'alignfull', 'not-prose', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->image)) { ?>
        <div class="trip-page-header__media">
            <div class="trip-page-header__media-inner img-fit">
                <?= $this->image; ?>
            </div>
        </div>
    <?php } ?>

    <div class="trip-page-header__content color-context-navy">
        <div class="trip-page-header__inner content-width-fluid-lg">
            <div class="trip-page-header__layout">

                <?php if (! empty($this->heading)) { ?>
                    <?= \Gust\Components\Heading::make(
                        heading: $this->heading,
                        el: 'h1',
                        classes: ['trip-page-header__heading', 'is-style-type-h1'],
                    ); ?>
                <?php } ?>

                <div class="trip-page-header__info-row">
                    <?php if (! empty($this->summary_items)) { ?>
                        <ul class="trip-page-header__summary" aria-label="<?= esc_attr__('Trip summary', 'gust'); ?>">
                            <?php foreach ($this->summary_items as $item) { ?>
                                <li class="trip-page-header__summary-item trip-page-header__summary-item--<?= esc_attr($item['icon']); ?>">
                                    <span class="trip-page-header__summary-icon" aria-hidden="true">
                                        <?php if ($item['icon'] === 'calendar') { ?>
                                            <?= SVG::get('icons/calendar.svg', ['fill' => 'var(--color-white)']); ?>
                                        <?php } elseif ($item['icon'] === 'location') { ?>
                                            <?= SVG::get('icons/location.svg', ['fill' => 'var(--color-white)']); ?>
                                        <?php } else { ?>
                                            <span class="trip-page-header__summary-currency">£</span>
                                        <?php } ?>
                                    </span>
                                    <span class="trip-page-header__summary-label"><?= esc_html($item['label']); ?></span>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>

                    <?php if (! empty($this->cta)) { ?>
                        <div class="trip-page-header__actions">
                            <?= \Gust\Components\Link::make(
                                title: $this->cta['label'],
                                url: $this->cta['url'],
                                classes: ['btn', 'trip-page-header__cta'],
                            ); ?>
                        </div>
                    <?php } ?>
                </div>

                <?php if (! empty($this->description)) { ?>
                    <div class="trip-page-header__description">
                        <?= wp_kses_post($this->description); ?>
                    </div>
                <?php } ?>

                <?php if (! empty($this->stats)) { ?>
                    <div class="trip-page-header__stats">
                        <?php foreach ($this->stats as $stat) { ?>
                            <div class="trip-page-header__stat">
                                <div class="trip-page-header__stat-label"><?= esc_html($stat['label']); ?></div>
                                <div class="trip-page-header__stat-value"><?= esc_html($stat['value']); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</section>
