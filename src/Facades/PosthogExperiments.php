<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\Facades;

use CarAndClassic\PosthogExperiments\PosthogExperimentsService;
use Illuminate\Support\Facades\Facade;

class PosthogExperiments extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PosthogExperimentsService::class;
    }
}
