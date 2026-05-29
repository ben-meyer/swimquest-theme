<?php
/**
 * CalendarListings Template
 *
 * @var \Gust\Components\CalendarListings $this
 */

use Theme\Utils\TripData;
?>

<div class="<?= classes('calendar-listings', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php foreach ($this->groups as $group) { ?>
        <section class="calendar-listings__group content-width-md">
            <?= \Gust\Components\Heading::make(
                heading: $group['heading'],
                classes: ['calendar-listings__heading', 'type-h4'],
            ); ?>

            <div class="calendar-listings__items">
                <?php foreach ($group['items'] as $item) {
                    $post = $item['object'];
                    $dateRow = $item['date_row'];
                    $locationHtml = TripData::getLocationHtml($post->ID);
                ?>
                    <div class="calendar-listings__item content-width-full">
                        <div class="calendar-listings__item-date">
                            <span class="calendar-listings__item-icon">
                                <?= \Gust\SVG::get('icons/calendar.svg'); ?>
                            </span>
                            <?= esc_html($dateRow['label']); ?>
                        </div>

                        <?php if (! empty($locationHtml)) { ?>
                            <div class="calendar-listings__item-location">
                                <span class="calendar-listings__item-icon">
                                    <?= \Gust\SVG::get('icons/location.svg'); ?>
                                </span>
                                <span><?= $locationHtml; ?></span>
                            </div>
                        <?php } ?>

                        <div class="calendar-listings__item-title">
                            <a href="<?= esc_url(get_permalink($post)); ?>">
                                <?= esc_html(get_the_title($post)); ?>
                            </a>
                        </div>

                        <div class="calendar-listings__item-action">
                            <?php if ($dateRow['status'] === 'bookable') { ?>
                                <?= \Gust\Components\Link::make(
                                    title: __('View & Book', 'gust'),
                                    url: get_permalink($post),
                                    classes: ['btn', 'btn--sm', 'calendar-listings__btn', 'color-context-orange'],
                                ); ?>
                            <?php } else { ?>
                                <?= \Gust\Components\Link::make(
                                    title: __('View Trip', 'gust'),
                                    url: get_permalink($post),
                                    classes: ['btn', 'btn--sm', 'btn--theme-2', 'calendar-listings__btn'],
                                ); ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
</div>
