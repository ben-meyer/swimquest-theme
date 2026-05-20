// Strip the Stretchy Heading variation before the block is registered.
// Using the blocks.registerBlockType filter (not wp.domReady + unregisterBlockVariation)
// because stretchy variations are defined inline in the block's variations array — they're
// registered synchronously at block-library load time, which can happen before domReady fires.
// This filter runs at registration time, so the variation never reaches the inserter.
wp.hooks.addFilter('blocks.registerBlockType', 'gust/remove-stretchy-heading', (settings, name) => {
    if (name === 'core/heading') {
        settings.variations = settings.variations?.filter((v) => v.name !== 'stretchy-heading');
    }
    return settings;
});

/**
 * Register block styles for core/heading block.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h2',
        label: 'H2 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h3',
        label: 'H3 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h4',
        label: 'H4 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h5',
        label: 'H5 Appearance',
    });
});
