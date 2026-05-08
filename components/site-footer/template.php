<?php
use Gust\Components\Image;

?>

<footer class="<?= classes('site-footer', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-footer__inner content-width-fluid-lg">
        <?php if (! empty($this->featured_in_logos)) { ?>
            <?= \Gust\Components\LogoGrid::make(
                logos: $this->featured_in_logos,
                featured_text: $this->featured_in_heading,
                classes: ['site-footer__featured'],
            ); ?>
        <?php } ?>

        <div class="site-footer__top alignwide type-small">
            <?php if ($top_text = get_field('footer_text_top', 'option')) { ?>
                <div class="site-footer__top-text">
                    <?= wp_kses_post($top_text); ?>
                    <?= \Gust\Components\SocialIcons::make(
                        // translators: 1: Social network name.
                        title: __('Visit our %s page', 'gust'),
                    ); ?>
                </div>
            <?php } ?>

            <?= \Gust\Components\Menu::make(
                theme_location: 'footer-1',
                max_depth: 1,
                classes: [
                    'site-footer__menu',
                    'site-footer__menu-1',
                    'type-small',
                ],
                heading: true,
                aria_label: __('Footer', 'gust'),
            ); ?>

            <div class="site-footer__right">
                <?php if ($footer_form = get_field('footer_form', 'option')) { ?>
                    <div class="site-footer__form">
                        <?= do_shortcode(wp_kses_post($footer_form)); ?>
                    </div>
                <?php } ?>

                <?php if (! empty($this->content['images'])) { ?>
                    <div class="site-footer__images flex-grid">
                        <?php foreach ($this->content['images'] as $image) { ?>
                            <?php if (! empty($image['link_args'])) { ?>
                                <?= \Gust\Components\Link::make(...$image['link_args']); ?>
                            <?php } else { ?>
                                <div class="site-footer__image img-fit">
                                    <?= Image::make(...$image['image']); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="site-footer__bottom">
            <div class="site-footer__bottom__inner alignwide">
                <?php if ($bottom_text = get_field('footer_text_bottom', 'option')) { ?>
                    <div class="site-footer__bottom-text type-small">
                        <?= wp_kses_post($bottom_text); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</footer>
