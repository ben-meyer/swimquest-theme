<?= \Gust\Components\TripCards::make(
    heading: __('You might be interested in', 'gust'),
    items: $this->items,
    columns: '3',
    classes: ['trip-related-trips'],
); ?>
