<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Utils\TripData;

class TripPageHeader extends ComponentBase
{
    protected static string $name = 'trip-page-header';

    public static function make(
        int $post_id = 0,
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $postId = ! empty($args['post_id']) ? (int) $args['post_id'] : \get_the_ID();
        $post = \get_post($postId);

        if (! $post) {
            return $args;
        }

        $heading = self::getArgOrField($args, 'heading', 'trip_heading', $postId);
        $description = self::getArgOrField($args, 'description', 'trip_description', $postId);
        $image = self::getArgOrField($args, 'image', 'trip_header_image', $postId);

        $durationNights = self::getArgOrField($args, 'duration_nights', 'duration_nights', $postId);
        $distanceMin = self::getArgOrField($args, 'distance_min_km', 'distance_min_km', $postId);
        $distanceMax = self::getArgOrField($args, 'distance_max_km', 'distance_max_km', $postId);
        $waterTempMin = self::getArgOrField($args, 'water_temp_min_c', 'water_temp_min_c', $postId);
        $waterTempMax = self::getArgOrField($args, 'water_temp_max_c', 'water_temp_max_c', $postId);
        $maxGroupSize = self::getArgOrField($args, 'max_group_size', 'max_group_size', $postId);
        $nonSwimmersText = self::getArgOrField($args, 'non_swimmers_text', 'non_swimmers_text', $postId);
        $techniqueCoachingText = self::getArgOrField($args, 'technique_coaching_text', 'technique_coaching_text', $postId);

        if ($nonSwimmersText === null) {
            $nonSwimmersText = self::getArgOrField($args, 'welcome_text', 'welcome_text', $postId);
        }

        $imageId = $image['ID'] ?? $image['id'] ?? \get_post_thumbnail_id($postId);

        if (! empty($imageId)) {
            $args['image'] = Image::make(
                id: $imageId,
                size: 'gust_super',
                sizes: '100vw',
            );
        }

        $args['heading'] = $heading ?: $post->post_title;
        $args['description'] = $description ?? '';
        $args['cta'] = TripData::getDateRows($postId) ? [
            'label' => __('View dates & book', 'gust'),
            'url' => '#trip-dates',
        ] : null;

        $args['summary_items'] = array_values(array_filter([
            [
                'icon' => 'calendar',
                'label' => TripData::getHeaderDateLabel($postId),
            ],
            [
                'icon' => 'location',
                'label' => TripData::getLocationLabel($postId),
            ],
            [
                'icon' => 'price',
                'label' => TripData::getPriceFromLabel($postId),
            ],
        ], fn ($item) => ! empty($item['label'])));

        $args['stats'] = array_values(array_filter([
            [
                'value' => ! empty($durationNights) ? $durationNights.' '.__('Nights', 'gust') : null,
                'label' => __('Duration', 'gust'),
            ],
            [
                'value' => self::formatRange($distanceMin, $distanceMax, 'km'),
                'label' => __('Distance per day', 'gust'),
            ],
            [
                'value' => self::formatRange($waterTempMin, $waterTempMax, '°C'),
                'label' => __('Water temperature', 'gust'),
            ],
            [
                'value' => $maxGroupSize,
                'label' => __('Max. group size', 'gust'),
            ],
            [
                'value' => TripData::getTaxonomyLabel($postId, 'skill_level'),
                'label' => __('Ability level', 'gust'),
            ],
            [
                'value' => TripData::getTaxonomyLabel($postId, 'swim_type'),
                'label' => __('Swim type', 'gust'),
            ],
            [
                'value' => $nonSwimmersText,
                'label' => __('Non-swimming Partners', 'gust'),
            ],
            [
                'value' => $techniqueCoachingText,
                'label' => __('Technique coaching', 'gust'),
            ],
        ], fn ($item) => ! empty($item['value'])));

        return $args;
    }

    protected static function getArgOrField(array $args, string $argKey, string $fieldName, int $postId): mixed
    {
        if (array_key_exists($argKey, $args) && $args[$argKey] !== null && $args[$argKey] !== '') {
            return $args[$argKey];
        }

        return \get_field($fieldName, $postId);
    }

    protected static function formatRange(mixed $min, mixed $max, string $suffix): ?string
    {
        $hasMin = $min !== null && $min !== '';
        $hasMax = $max !== null && $max !== '';

        if (! $hasMin && ! $hasMax) {
            return null;
        }

        if ($hasMin && $hasMax) {
            if ((string) $min === (string) $max) {
                return $min.$suffix;
            }

            return $min.'-'.$max.$suffix;
        }

        return ($hasMin ? $min : $max).$suffix;
    }
}
