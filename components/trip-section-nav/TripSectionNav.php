<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Utils\TripData;

class TripSectionNav extends ComponentBase
{
    protected static string $name = 'trip-section-nav';

    public static function make(
        int $post_id = 0,
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        $postId = ! empty($args['post_id']) ? (int) $args['post_id'] : \get_the_ID();

        return ! empty(TripData::getSectionMap($postId));
    }

    protected static function transform(array $args): array
    {
        $postId = ! empty($args['post_id']) ? (int) $args['post_id'] : \get_the_ID();

        $args['items'] = TripData::getSectionMap($postId);
        $args['enquiry_action'] = TripData::getPrimaryEnquiryAction($postId);
        $args['booking_action'] = TripData::getPrimaryBookingAction($postId);

        return $args;
    }
}
