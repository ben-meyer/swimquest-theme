<?php
/**
 * Gallery Template
 *
 * @var \Gust\Components\Gallery $this
 */

use Gust\Helpers;
use Gust\Components\Heading;
use Gust\Components\Image;

$image_count = count($this->images ?? []);
?>

<sq-gallery class="<?= classes('wp-block', 'alignfull', $this->classes) ?>"<?= Helpers::buildAttributes($this->attributes); ?>>
    <!-- Gallery content -->
    <div class="gallery__inner content-width-fluid-lg">

        <?php if (! empty($this->images)) { ?>
            <div class="gallery__swiper-wrap">
                <?php if ($image_count > 1) { ?>
                    <button type="button" class="gallery__prev btn btn--ghost" aria-label="Previous slide">
                        <span class="btn__icon" style="--btn--icon: url('<?= staticUrl('images/icons/chevron-right.svg') ?>')"></span>
                    </button>

                    <button type="button" class="gallery__next btn btn--ghost" aria-label="Next slide">
                        <span class="btn__icon" style="--btn--icon: url('<?= staticUrl('images/icons/chevron-right.svg') ?>')"></span>
                    </button>
                <?php } ?>

                <div class="gallery__swiper swiper">
                    <div class="swiper-wrapper">
                    <?php foreach ($this->images as $item) { ?>
                        <?php if (! empty($item['image'])) {
                            $image_id = $item['image']['ID'] ?? $item['image']['id'] ?? null;
                            ?>
                            <div class="gallery__slide swiper-slide">
                                <?= Image::make(
                                    id: $image_id,
                                    size: 'large',
                                    classes: ['gallery__image'],
                                    show_caption: true,
                                    show_credit: true,
                                ); ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</sq-gallery>
