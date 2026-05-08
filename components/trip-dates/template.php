<section class="<?= classes('trip-dates', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div id="trip-dates" class="trip-dates__inner content-width-sm align-left w-full">
        <h4 class="trip-dates__heading"><?= esc_html__('Dates & book', 'gust') ?></h4>

        <?php if (! empty($this->date_rows)) { ?>
            <ul class="trip-dates__list">
                <?php foreach ($this->date_rows as $row) { ?>
                    <li class="trip-dates__item <?= $row['is_sold_out'] ? 'is-sold-out' : '' ?>">
                        <h6 class="trip-dates__item__label">
                            <?= esc_html($row['label']) ?>
                        </h6>

                        <?php if ($row['is_bookable']) { ?>
                            <a href="<?= esc_url($row['booking_url']) ?>" class="trip-dates__item__cta btn" target="_blank" rel="noopener">
                                <?= esc_html__('Book Now', 'gust') ?>
                            </a>
                        <?php } elseif ($row['is_sold_out']) { ?>
                            <button class="trip-dates__item__cta trip-dates__item__cta--sold-out btn btn--inactive" type="button" disabled>
                                <?= esc_html($row['sold_out_label']) ?>
                            </button>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } elseif ($this->is_preview) { ?>
            <p class="trip-dates__empty"><?= esc_html__('No departure dates added yet. Add dates in the Trip Fields panel.', 'gust') ?></p>
        <?php } ?>
    </div>
</section>
