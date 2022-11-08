<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFeatureFlagCalledJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private string $experiment,
        private string $featureFlag = '',
        private string|int $participant = ''
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (empty($this->featureFlag) || empty($this->participant)) {
            return;
        }

        $domain = trim(config('posthog-experiments.domain'), '/');
        $resp = Http::post("{$domain}/capture", [
            'api_key' => config('posthog-experiments.key'),
            'event' => '$feature_flag_called',
            'properties' => [
                'distinct_id' => $this->participant,
                '$feature_flag_response' => $this->featureFlag,
                '$feature_flag' => $this->experiment,
            ],
            'timestamp' => date(DATE_ISO8601),
        ]);

        if ($resp->failed()) {
            Log::error('Failed to send the PostHog $feature_flag_called event', ['experiment' => $this->experiment, 'participant' => $this->participant, 'featureFlag' => $this->featureFlag]);
        }
    }
}
