<?php
echo \Gust\Components\TripPageHeader::make(post_id: $this->post_id);
echo \Gust\Components\TripSectionNav::make(post_id: $this->post_id);
echo \Gust\Components\TripIntro::make(post_id: $this->post_id);
echo \Gust\Components\Gallery::make(images: $this->intro_gallery);
echo \Gust\Components\TripHighlights::make(post_id: $this->post_id);
echo \Gust\Components\TripItineraryPreview::make(post_id: $this->post_id);
echo \Gust\Components\Gallery::make(images: $this->mid_gallery);
echo \Gust\Components\TripAccommodationPreview::make(post_id: $this->post_id);
echo \Gust\Components\TripIncludes::make(post_id: $this->post_id);
echo \Gust\Components\TripGettingThere::make(post_id: $this->post_id);
echo \Gust\Components\TripReviews::make(post_id: $this->post_id);

if (! empty($this->faqs)) {
    echo \Gust\Components\Accordion::make(
        id: 'trip-faqs',
        heading: __('FAQs', 'gust'),
        accordion_items: array_map(fn ($item) => [
            'title' => $item['question'] ?? '',
            'content' => $item['answer'] ?? '',
        ], $this->faqs),
    );
}

echo \Gust\Components\TripDates::make(post_id: $this->post_id);
echo \Gust\Components\Promo::make(post_id: $this->post_id);
echo \Gust\Components\TripGetInTouch::make(post_id: $this->post_id);
echo \Gust\Components\TripRelatedStories::make(post_id: $this->post_id);
echo \Gust\Components\TripRelatedTrips::make(post_id: $this->post_id);
