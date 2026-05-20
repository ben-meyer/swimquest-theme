wp.hooks.addFilter('blocks.registerBlockType', 'gust/list-supports', (settings, name) => {
    if (name === 'core/list') {
        return {
            ...settings,
            supports: {
                ...settings.supports,
                color: false,
            },
        };
    }
    return settings;
});
