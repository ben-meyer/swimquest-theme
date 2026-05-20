<section class="<?= classes('trip-dates', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div id="trip-dates" class="trip-dates__inner content-width-sm align-left w-full">
        <h4 class="trip-dates__heading"><?= esc_html__('Dates & book', 'gust') ?></h4>

        <?php if (! empty($this->date_rows)) { ?>
            <ul class="trip-dates__list">
                <?php foreach ($this->date_rows as $row) { ?>
                    <li class="trip-dates__item <?= $row['is_sold_out'] ? 'is-sold-out' : '' ?>">
                        <div class="trip-dates__item__meta">
                            <h6 class="trip-dates__item__label">
                                <?= esc_html($row['label']) ?>
                                <?php if ($row['nights']) { ?>
                                    <span class="trip-dates__item__nights">(<?= esc_html($row['nights']) ?> nights)</span>
                                <?php } ?>
                            </h6>

                            <?php if ($row['price_display']) { ?>
                                <span class="trip-dates__item__price"><?= esc_html($row['price_display']) ?></span>
                            <?php } ?>
                        </div>

                        <?php if ($row['enquiry_url'] || $row['is_bookable'] || $row['is_sold_out']) { ?>
                            <div class="trip-dates__item__actions">
                                <?php if ($row['enquiry_url']) { ?>
                                    <a href="<?= esc_url($row['enquiry_url']) ?>" class="trip-dates__item__cta button btn btn--secondary" target="_blank" rel="noopener">
                                        <?= esc_html__('Enquire', 'gust') ?>
                                    </a>
                                <?php } ?>

                                <?php if ($row['is_bookable']) { ?>
                                    <a href="<?= esc_url($row['booking_url']) ?>" class="trip-dates__item__cta button btn" target="_blank" rel="noopener">
                                        <?= esc_html__('Book Now', 'gust') ?>
                                    </a>
                                <?php } elseif ($row['is_sold_out']) { ?>
                                    <button class="trip-dates__item__cta trip-dates__item__cta--sold-out button btn" type="button" disabled>
                                        <?= esc_html($row['sold_out_label']) ?>
                                    </button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } elseif ($this->is_preview) { ?>
            <p class="trip-dates__empty"><?= esc_html__('No departure dates added yet. Add dates in the Trip Fields panel.', 'gust') ?></p>
        <?php } ?>
    </div>
</section>
