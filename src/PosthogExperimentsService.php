<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments;

use CarAndClassic\PosthogExperiments\Jobs\SendFeatureFlagCalledJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PosthogExperimentsService
{
    public static function getFeatureFlag(string $experiment, string|int $participant = '', string $override = ''): string
    {
        if (empty($participant)) {
            $userId = auth()->id();
            $cookie = request()->cookie(config('posthog-experiments.cookie_key'));
            $cookieAnonymised = md5($cookie);

            if ($userId && cache()->has($experiment.$cookieAnonymised)) {
                $participant = $userId;
                self::setAlias(md5((string) $participant), $cookieAnonymised);
            } elseif ($userId) {
                $participant = $userId;
            } else {
                $participant = $cookie;
            }
        }

        if (! $participant) {
            return '';
        }

        $participantAnonymised = md5((string) $participant);

        if ($override) {
            SendFeatureFlagCalledJob::dispatch($experiment, $override, $participantAnonymised);

            return $override;
        }

        $featureFlag = cache()->rememberForever(
            $experiment.$participantAnonymised,
            static function () use ($experiment, $participantAnonymised): string {
                $domain = trim(config('posthog-experiments.domain'), '/');
                $resp = Http::post("{$domain}/decide?v=2", [
                    'api_key' => config('posthog-experiments.key'),
                    'distinct_id' => $participantAnonymised,
                ]);

                if ($resp->failed()) {
                    Log::error('Failed to get a PostHog feature flag', ['experiment' => $experiment, 'participant' => $participantAnonymised]);

                    return '';
                }

                return $resp->json("featureFlags.{$experiment}") ?? '';
            }
        );

        if (empty($featureFlag)) {
            cache()->forget($experiment.$participantAnonymised);

            return '';
        }

        SendFeatureFlagCalledJob::dispatch($experiment, $featureFlag, $participantAnonymised);

        return $featureFlag;
    }

    public static function hasFeatureFlag(string $experiment, string|array $featureFlag, string|int $participant = ''): bool
    {
        if (is_string($featureFlag)) {
            $featureFlag = [$featureFlag];
        }

        return in_array(self::getFeatureFlag($experiment, $participant), $featureFlag);
    }

    private static function setAlias(string|int $participant = '', string $alias = ''): void
    {
        if (empty($participant) || empty($alias)) {
            return;
        }

        $domain = trim(config('posthog-experiments.domain'), '/');
        $resp = Http::post("{$domain}/capture", [
            'api_key' => config('posthog-experiments.key'),
            'properties' => [
                'distinct_id' => $participant,
                'alias' => $alias,
            ],
            'timestamp' => date(DATE_ISO8601),
            'context' => '{}',
            'type' => 'alias',
            'event' => '$create_alias',
        ]);

        if ($resp->failed()) {
            Log::error('Failed to send a PostHog alias', ['participant' => $participant, 'alias' => $alias]);
        }
    }
}
