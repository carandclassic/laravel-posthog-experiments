<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\View\Components;

use CarAndClassic\PosthogExperiments\PosthogExperiments;
use Illuminate\View\Component;

class Experiment extends Component
{
    public function __construct(
        private string $experiment,
        private string $participant = '',
        private string $featureFlag = '',
    ) {
        $this->featureFlag = PosthogExperiments::getFeatureFlag(
            $experiment,
            $participant,
            request()->input('posthog', '')
        );
    }

    public function render(): \Closure
    {
        return function (array $data): string {
            return $data['__laravel_slots'][$this->featureFlag]?->toHtml()
                ?? $data['slot']?->toHtml()
                ?: $data['__laravel_slots']['control']?->toHtml()
                ?? '';
        };
    }
}
