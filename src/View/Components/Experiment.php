<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\View\Components;

use CarAndClassic\PosthogExperiments\PosthogExperimentsService;
use Illuminate\View\Component;

class Experiment extends Component
{
    private string $featureFlag = '';

    public function __construct(string $experiment, string $participant = '')
    {
        $this->featureFlag = PosthogExperimentsService::getFeatureFlag(
            $experiment,
            $participant,
            request()->input(config('posthog-experiments.override_query_parameter'), '')
        );
    }

    public function render(): \Closure
    {
        return fn (array $data): string => (string)collect([
            $data['__laravel_slots'][$this->featureFlag]?->toHtml(),
            $data['slot']?->toHtml(),
            $data['__laravel_slots']['control']?->toHtml(),
        ])
            ->filter()
            ->first();
    }
}
