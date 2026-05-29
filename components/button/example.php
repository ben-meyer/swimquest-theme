<?php

/**
 * Button Component Examples
 */

use Gust\Components\Button;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Primary Button</h2>
    <p class="component-example-section__description">Primary button with text content.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'Primary Button', classes: ['btn']); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Secondary Button</h2>
    <p class="component-example-section__description">Secondary button with text content.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'Secondary Button', classes: ['btn', 'btn--theme-2']); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Inactive Button</h2>
    <p class="component-example-section__description">Inactive button with text content.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'Inactive Button', classes: ['btn', 'btn--inactive']); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Ghost Button</h2>
    <p class="component-example-section__description">Ghost button with text content.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'Ghost Button', classes: ['btn', 'btn--ghost']); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">No class passed</h2>
    <p class="component-example-section__description">Default button with no variant class passed.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'No class variant passed', classes: ['btn']); ?>
    </div>
</section>

<!-- have to pass in 'btn' and the variant class, e.g., 'btn--theme-2' -->

<section class="component-example-section">
    <h2 class="component-example-section__title">Direct class application</h2>
    <p class="component-example-section__description">Button with a directly applied class.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'Secondary button', classes: ['btn', 'btn--theme-2']); ?>
    </div>
</section>

<!-- 
<section class="component-example-section">
    <h2 class="component-example-section__title">Button Types</h2>
    <p class="component-example-section__description">Different button types: button, submit, reset.</p>
    <div class="component-example-section__preview" style="display: flex; gap: 1rem;">
        <?= Button::make(content: 'Button', type: 'button'); ?>
        <?= Button::make(content: 'Submit', type: 'submit'); ?>
        <?= Button::make(content: 'Reset', type: 'reset'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Screen Reader Text</h2>
    <p class="component-example-section__description">Button with visually hidden text for accessibility.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'Close menu', screen_reader_text: true); ?>
    </div>
</section> -->
