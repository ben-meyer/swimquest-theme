// Strip the Stretchy Heading variation and disable Typography/Colour panels before the block
// is registered. Using blocks.registerBlockType filter (not wp.domReady) because variations
// and supports are set synchronously at block-library load time — this filter runs at
// registration time so changes take effect before the inserter/inspector renders.
wp.hooks.addFilter('blocks.registerBlockType', 'gust/heading-supports', (settings, name) => {
    if (name === 'core/heading') {
        settings.variations = settings.variations?.filter((v) => v.name !== 'stretchy-heading');
        return {
            ...settings,
            supports: {
                ...settings.supports,
                typography: false,
                color: false,
            },
        };
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
