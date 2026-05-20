<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * TripDates Component
 *
 * Server-side rendered block that displays departure dates for the current trip.
 * Reads dates from the 'dates' ACF repeater on the current post — no block fields.
 *
 * Usage: Block only — acf/trip-dates in the trip editor template.
 */
class TripDates extends ComponentBase
{
    protected static string $name = 'trip-dates';

    public static function make(
        int $post_id = 0,
        bool $is_preview = false,
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        $postId = ! empty($args['post_id']) ? (int) $args['post_id'] : \get_the_ID();

        return ! empty(\get_field('dates', $postId));
    }

    protected static function transform(array $args): array
    {
        $post_id = ! empty($args['post_id']) ? (int) $args['post_id'] : \get_the_ID();

        $rows = \get_field('dates', $post_id) ?: [];

        $args['date_rows'] = array_map(function (array $row) {
            $start = $row['start_date'] ?? '';
            $end   = $row['end_date']   ?? '';

            $start_timestamp = $start ? \strtotime($start) : false;
            $end_timestamp   = $end ? \strtotime($end) : false;
            $same_month      = $start_timestamp && $end_timestamp
                && \date('Y-m', $start_timestamp) === \date('Y-m', $end_timestamp);

            $fmt_start = $start_timestamp ? \date_i18n($same_month ? 'j' : 'j M', $start_timestamp) : '';
            $fmt_end   = $end_timestamp ? \date_i18n('j M Y', $end_timestamp) : '';

            $label = $fmt_start;
            if ($fmt_end && $fmt_end !== $fmt_start) {
                $label .= ' – '.$fmt_end;
            }

            $nights = null;
            if ($start && $end) {
                $nights = (int) \date_diff(\date_create($start), \date_create($end))->days;
            }

            $price         = isset($row['price']) && $row['price'] !== '' ? (float) $row['price'] : null;
            $price_display = $price !== null ? '£'.number_format($price, 0) : null;

            $status      = $row['status'] ?? 'bookable';
            $booking_url = ! empty($row['booking_url']) ? $row['booking_url'] : null;
            $enquiry_url = ! empty($row['enquiry_url']) ? $row['enquiry_url'] : null;

            return [
                'label'         => $label,
                'nights'        => $nights,
                'price_display' => $price_display,
                'status'        => $status,
                'booking_url'   => $booking_url,
                'enquiry_url'   => $enquiry_url,
                'is_bookable'   => $status === 'bookable' && ! empty($booking_url),
                'is_sold_out'      => \in_array($status, ['sold_out', 'sold_out_private'], true),
                'is_private_group' => $status === 'sold_out_private',
                'sold_out_label'   => $status === 'sold_out_private' ? __('Private Group', 'gust') : __('Sold Out', 'gust'),
            ];
        }, $rows);

        return $args;
    }
}
