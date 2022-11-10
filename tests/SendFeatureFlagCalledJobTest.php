<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\Tests;

use CarAndClassic\PosthogExperiments\Jobs\SendFeatureFlagCalledJob;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class SendFeatureFlagCalledJobTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    public function testItMakesACallToTheCaptureEndpoint()
    {
        (new SendFeatureFlagCalledJob('test', 'test_a', '1'))->handle();

        Http::assertSent(function (Request $request) {
            $domain = config('posthog-experiments.domain');
            $this->assertSame($request->url(), "{$domain}/capture");

            return true;
        });
    }

    public function testItReturnsEarlyWhenNoFeatureFlagOrParticipantIsPassedIn()
    {
        (new SendFeatureFlagCalledJob('test'))->handle();

        Http::assertNotSent(function (Request $request) {
            $domain = config('posthog-experiments.domain');
            $this->assertSame($request->url(), "{$domain}/capture");

            return true;
        });
    }
}
