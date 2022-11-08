<?php

namespace CarAndClassic\PosthogExperiments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CarAndClassic\PosthogExperiments\PosthogExperiments
 */
class PosthogExperiments extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \CarAndClassic\PosthogExperiments\PosthogExperiments::class;
    }
}
