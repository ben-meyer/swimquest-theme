<?php if (function_exists('yoast_breadcrumb')) { ?>
    <nav class="<?= classes('breadcrumbs', $this->classes) ?>" aria-label="<?= esc_attr__('Breadcrumb', 'gust') ?>" <?= attributes($this->attributes) ?>>
        <?php \yoast_breadcrumb('', ''); ?>
    </nav>
<?php } ?>
