// Same pattern as stretchy-heading above — filter at registration time, not domReady.
wp.hooks.addFilter('blocks.registerBlockType', 'gust/remove-stretchy-paragraph', (settings, name) => {
    if (name === 'core/paragraph') {
        settings.variations = settings.variations?.filter((v) => v.name !== 'stretchy-paragraph');
    }
    return settings;
});

/**
 * Register block styles for core/paragraph block.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/paragraph', {
        name: 'type-small',
        label: 'Small',
    });

    wp.blocks.registerBlockStyle('core/paragraph', {
        name: 'type-regular',
        label: 'Regular',
        isDefault: true,
    });

    wp.blocks.registerBlockStyle('core/paragraph', {
        name: 'type-large',
        label: 'Large',
    });

    wp.blocks.unregisterBlockStyle('core/paragraph', 'default');
});
