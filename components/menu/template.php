<nav class="<?= classes('menu', $this->classes) ?>"<?= $this->aria_label ? ' aria-label="'.esc_attr($this->aria_label).'"' : '' ?> <?= attributes($this->attributes) ?>>
    <div class="menu__inner">
        <?php if (! empty($this->heading)) { ?>
            <?= \Gust\Components\Heading::make(
                heading: $this->heading,
                classes: ['menu__heading'],
            ); ?>
        <?php } ?>

        <?= \Gust\Components\Menu\MenuList::make(
            items: $this->items,
            id: $this->menu_id,
            max_depth: $this->max_depth,
        ); ?>
    </div>
</nav>
