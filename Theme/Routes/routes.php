<?php

/**
 * Application Routes
 *
 * Define routes using:
 * - Router::route() for owned routes (custom URLs)
 * - Router::decorate() for decorated routes (WordPress archives)
 *
 * @see agents/2026-01-08-application-routing-system-pages.md
 */

use Gust\Router;
use Theme\Controllers\CalendarController;
use Theme\Controllers\DestinationsController;
use Theme\Controllers\NotFoundController;
use Theme\Controllers\SearchController;
use Theme\Controllers\TripStylesController;
use Theme\Modules\Events\EventsModule;

// Search results
Router::decorateSearch(SearchController::class)
    ->withPage('search')
    ->withSlot('template-content', fn () => SearchController::renderResults());

// 404 page
Router::decorate404(NotFoundController::class)
    ->withPage('404')
    ->withSlot('template-content', fn () => NotFoundController::renderContent());

// Events archive
Router::decoratePostType('events', EventsModule::class)
    ->withPage('events')
    ->withSlot('template-content', [EventsModule::class, 'renderArchive']);

// Trip Styles index (/trip-styles/) — lists all trip style terms as tiles
Router::route('/trip-styles', TripStylesController::class)
    ->withPage('trip-styles')
    ->withSlot('template-content', [TripStylesController::class, 'renderContent']);

// Trip Styles taxonomy archives (/trip-styles/%slug%/)
Router::decorateTaxonomy('trip_style', \Theme\Controllers\ArchiveController::class)
    ->withPage('trip-style-archive')
    ->withSlot('template-content', [\Theme\Controllers\ArchiveController::class, 'renderLoop']);

// Destinations taxonomy archives (/destinations/%slug%/)
Router::decorateTaxonomy('country', \Theme\Controllers\ArchiveController::class)
    ->withPage('country-archive')
    ->withSlot('template-content', [\Theme\Controllers\ArchiveController::class, 'renderLoop']);

// Locations taxonomy archives (/locations/%slug%/)
Router::decorateTaxonomy('location', \Theme\Controllers\ArchiveController::class)
    ->withPage('location-archive')
    ->withSlot('template-content', [\Theme\Controllers\ArchiveController::class, 'renderLoop']);

// Destinations index (/destinations/)
Router::route('/destinations', DestinationsController::class)
    ->withPage('destinations')
    ->withSlot('template-content', [DestinationsController::class, 'renderContent']);

// Calendar (/calendar/)
Router::route('/calendar', CalendarController::class)
    ->withPage('calendar')
    ->withSlot('template-content', [CalendarController::class, 'renderContent']);
