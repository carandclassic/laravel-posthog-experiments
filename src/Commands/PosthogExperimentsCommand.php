<?php

namespace CarAndClassic\PosthogExperiments\Commands;

use Illuminate\Console\Command;

class PosthogExperimentsCommand extends Command
{
    public $signature = 'posthog-experiments';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
