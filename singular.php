<?php

get_header();

$object = \Gust\WordPress\PageObject::get();

$site_main_classes = ['site-main', 'site-main--single'];

site_main_open(classes: $site_main_classes, object: $object);

if ($object instanceof \WP_Post && $object->post_type === 'trip') {
    echo \Gust\Components\TripSingle::make(object: $object);
} elseif ($object instanceof \WP_Post && $object->post_type === 'events') {
    echo \Gust\Components\EventSingle::make(object: $object);
} else {
    if (! \Gust\Helpers::hasPageHeaderBlock()) {
        echo \Gust\Components\PageHeader::make(object: $object);
    }

    while (have_posts()) {
        the_post();
        the_content();
    }

    if ($object instanceof \WP_Post && $object->post_type === 'itinerary') {
        echo '<div class="print-page-action wp-block alignwide">';
        echo \Gust\Components\Button::make(
            content: 'Print',
            classes: ['btn', 'print-page-action__btn'],
            attributes: ['onclick' => 'window.print()'],
        );
        echo '</div>';
    }
}

site_main_close();

get_footer();
