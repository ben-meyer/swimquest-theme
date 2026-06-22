<?php
/**
 * GetInTouch Template
 *
 * @var \Gust\Components\GetInTouch $this
 */
?>

<section class="<?= classes('get-in-touch', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="get-in-touch__inner">
        <h2 class="get-in-touch__heading"><?= esc_html__('Get in touch', 'gust') ?></h2>

        <div class="get-in-touch__contacts-wrapper">
            <ul class="get-in-touch__contacts">
                <?php foreach ($this->contacts as $contact): ?>
                    <li class="get-in-touch__contact get-in-touch__contact--<?= esc_attr($contact['icon']) ?>">
                        <?= \Gust\SVG::get(get_theme_file_path('public/build/images/icons/' . $contact['icon'] . '.svg'), ['asset' => false, 'width' => 16, 'height' => 16]) ?>

                        <span class="get-in-touch__contact__content">
                            <?php if (! empty($contact['value'])): ?>
                                <strong><?= esc_html($contact['label']) ?>:</strong>&nbsp;<?php if (! empty($contact['url'])): ?>
                                    <a href="<?= esc_url($contact['url']) ?>"><?= esc_html($contact['value']) ?></a>
                                <?php else: ?>
                                    <span><?= esc_html($contact['value']) ?></span>
                                <?php endif; ?>
                            <?php elseif (! empty($contact['url'])): ?>
                                <a href="<?= esc_url($contact['url']) ?>"<?= $contact['icon'] === 'whatsapp' ? ' target="_blank" rel="noopener"' : '' ?>>
                                    <?= esc_html($contact['label']) ?>
                                </a>
                            <?php else: ?>
                                <?= esc_html($contact['label']) ?>
                            <?php endif; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
