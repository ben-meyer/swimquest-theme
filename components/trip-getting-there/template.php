<section id="trip-getting-there" class="<?= classes('trip-getting-there', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="trip-getting-there__inner content-width-sm align-left w-full">
        <?= \Gust\Components\Heading::make(
            heading: __('Getting there', 'gust'),
            classes: ['trip-getting-there__heading'],
            el: 'h4',
        ); ?>

        <?php
            $tripFinishTime = '';

            foreach (array_reverse($this->stages) as $stageWithFinishTime) {
                if (! empty($stageWithFinishTime['finish_time'])) {
                    $tripFinishTime = (string) $stageWithFinishTime['finish_time'];
                    break;
                }
            }
        ?>

        <?php foreach ($this->stages as $stageIndex => $stage) {
            $isLastStage = $stageIndex === array_key_last($this->stages);
            $hasSteps = ! empty($stage['steps']) && is_array($stage['steps']);
        ?>
            <div class="trip-getting-there__stage">
                <?php if (! empty($stage['title'])) { ?>
                    <?= \Gust\Components\Heading::make(
                        heading: $stage['title'],
                        el: 'h5',
                    ); ?>
                <?php } ?>

                <?php if (! empty($stage['start_time'])): ?>
                    <h6 class="trip-getting-there__start-time">
                        <span 
                        class="trip-getting-there__start-time-icon" aria-hidden="true">
                            <?= \Gust\SVG::get(get_theme_file_path('public/build/images/icons/clock.svg'), ['asset' => false, 'width' => 16, 'height' => 16]); ?>
                        </span>
                        <span><?= esc_html($stage['start_time']); ?></span>
                    </h6>
                <?php endif; ?>

                <?php if ($hasSteps) { ?>
                    <div class="trip-getting-there__steps">
                        <?php foreach ($stage['steps'] as $stepIndex => $step) {
                            $isLastStep = $stepIndex === array_key_last($stage['steps']);
                        ?>
                            <article class="trip-getting-there__step">
                                <?php if (! empty($step['title'])) { ?>
                                    <?php
                                        $icon = ! empty($step['icon']) ? strtolower((string) $step['icon']) : '';
                                        $iconMap = [
                                            'plane' => 'plane.svg',
                                            'ferry' => 'ferry.svg',
                                            'car' => 'car.svg',
                                            'bus' => 'bus.svg',
                                            'train' => 'train.svg',
                                            'walk' => 'walking.svg',
                                        ];
                                        $iconFile = $iconMap[$icon] ?? 'car.svg';
                                    ?>
                                    <h6 class="trip-getting-there__step-title">
                                        <span class="trip-getting-there__step-title-icon" aria-hidden="true">
                                            <?= \Gust\SVG::get(get_theme_file_path('public/build/images/icons/' . $iconFile), ['asset' => false, 'width' => 16, 'height' => 16]); ?>
                                        </span>
                                        <span><?= esc_html($step['title']); ?></span>
                                    </h6>
                                <?php } ?>

                                <?php if (! empty($step['description'])) { ?>
                                    <div class="trip-getting-there__step-description"><?= wp_kses_post($step['description']); ?></div>
                                <?php } ?>

                                <?php if ($isLastStage && $isLastStep && ! empty($tripFinishTime)): ?>
                                    <h6 class="trip-getting-there__finish-time">
                                        <span class="trip-getting-there__finish-time-icon" aria-hidden="true">
                                            <?= \Gust\SVG::get(get_theme_file_path('public/build/images/icons/clock.svg'), ['asset' => false, 'width' => 16, 'height' => 16]); ?>
                                        </span>
                                        <span><?= esc_html($tripFinishTime); ?></span>
                                    </h6>
                                <?php endif; ?>
                            </article>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ($isLastStage && ! $hasSteps && ! empty($tripFinishTime)): ?>
                    <h6 class="trip-getting-there__finish-time">
                        <?= \Gust\SVG::get(get_theme_file_path('public/build/images/icons/clock.svg'), ['asset' => false, 'width' => 16, 'height' => 16]); ?>
                        <?= esc_html($tripFinishTime); ?>
                    </h6>
                <?php endif; ?>
            </div>
        <?php } ?>
    </div>
</section>
