<?php
/**
 * TripIntro Template
 *
 * @var \Gust\Components\TripIntro $this
 */
?>

<section class="<?= classes('trip-intro', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="trip-intro__inner content-width-sm align-left w-full">
        <?php if (! empty($this->lead)) {
            echo \Gust\Components\Element::make(
                el: 'div',
                content: $this->lead,
                classes: ['trip-intro__lead', 'is-style-type-large'],
            );
        } ?>
        <?php if (! empty($this->body)) {
            echo \Gust\Components\Element::make(
                el: 'div',
                content: $this->body,
                classes: ['trip-intro__body', 'is-style-type-regular'],
            );
        } ?>
    </div>
</section>
