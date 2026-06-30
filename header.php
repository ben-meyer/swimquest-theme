<!DOCTYPE html>
<html <?php
use Gust\Components\SiteHeader;
use Gust\Components\SkipLink;

language_attributes(); ?> class="no-js">
<head>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <?= SkipLink::make(); ?>
    <?= SiteHeader::make(); ?>
