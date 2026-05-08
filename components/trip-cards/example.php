<?php

/**
 * TripCards Component Examples
 */

use Gust\Components\TripCards;

// Get real trip posts for preview
$trips = get_posts([
    'post_type' => 'trip',
    'posts_per_page' => 6,
    'post_status' => 'publish',
]);

$items = array_map(fn ($trip) => ['object' => $trip], $trips);

?>

<?php if (! empty($items)) { ?>
    <section class="component-example-section">
        <h2 class="component-example-section__title">Trip Cards - With Heading</h2>
        <p class="component-example-section__description">Full block with heading, subheading, and trip cards grid.</p>
        <div class="component-example-section__preview">
            <?= TripCards::make(
                heading: 'First Timer?',
                subheading: '<p>Dip Your Toes</p>',
                items: array_slice($items, 0, 3),
            ); ?>
        </div>
    </section>

    <?php if (count($items) > 3) { ?>
        <section class="component-example-section">
            <h2 class="component-example-section__title">Trip Cards - 6 Items</h2>
            <p class="component-example-section__description">Full grid.</p>
            <div class="component-example-section__preview">
                <?= TripCards::make(
                    heading: 'Explore All Trips',
                    items: $items,
                ); ?>
            </div>
        </section>
    <?php } ?>
<?php } else { ?>
    <section class="component-example-section">
        <h2 class="component-example-section__title">Trip Cards</h2>
        <p class="component-example-section__description">No trip posts found. Create some trips to preview this component.</p>
    </section>
<?php } ?>
