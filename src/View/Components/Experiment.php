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
        return function (array $data): string {
            if (isset($data['__laravel_slots'][$this->featureFlag])) {
                return $data['__laravel_slots'][$this->featureFlag]->toHtml();
            }

            if (! empty($data['slot']->toHtml())) {
                return $data['slot']->toHtml();
            }

            if (isset($data['__laravel_slots']['control'])) {
                return $data['__laravel_slots']['control']->toHtml();
            }

            return '';
        };
    }
}
