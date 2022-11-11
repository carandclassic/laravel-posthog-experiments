<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments;

use CarAndClassic\PosthogExperiments\View\Components\Experiment;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PosthogExperimentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('posthog-experiments')
            ->hasConfigFile()
            ->hasViewComponent('posthog', Experiment::class);

        Blade::if('hasFeatureFlag', function (string $experiment, string|array $featureFlag, string|int $participant = '') {
            if (request()->has(config('posthog-experiments.override_query_parameter'))) {
                return request()->input(config('posthog-experiments.override_query_parameter'), '') === $featureFlag;
            }

            return PosthogExperimentsService::hasFeatureFlag($experiment, $featureFlag, $participant);
        });
    }
}
