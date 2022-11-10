<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments;

use CarAndClassic\PosthogExperiments\View\Components\Experiment;
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
    }
}
