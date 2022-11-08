<?php

namespace CarAndClassic\PosthogExperiments;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use CarAndClassic\PosthogExperiments\Commands\PosthogExperimentsCommand;

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
            ->hasViews()
            ->hasMigration('create_posthog-experiments_table')
            ->hasCommand(PosthogExperimentsCommand::class);
    }
}
