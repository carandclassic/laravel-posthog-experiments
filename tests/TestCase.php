<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\Tests;

use CarAndClassic\PosthogExperiments\PosthogExperimentsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            PosthogExperimentsServiceProvider::class,
        ];
    }
}
