<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\Tests;

use CarAndClassic\PosthogExperiments\PosthogExperimentsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PosthogExperimentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'test_a',
                ],
            ], 200),
        ]);
    }

    public function testItCanGetAFeatureFlag(): void
    {
        $experiment = 'test';
        $participant = '1';

        $featureFlag = PosthogExperimentsService::getFeatureFlag($experiment, $participant);

        $this->assertSame('test_a', $featureFlag);
        $this->assertTrue(Cache::has($experiment.md5($participant)));
    }

    public function testItCanTestIfSpecificFeatureFlagsAreSetByPassingInAnArray(): void
    {
        $experiment = 'test';
        $participant = '1';

        $hasFeatureFlag = PosthogExperimentsService::hasFeatureFlag($experiment, ['test_a'], $participant);

        $this->assertTrue($hasFeatureFlag);
    }

    public function testItCanTestIfSpecificFeatureFlagsAreSetByPassingInAString(): void
    {
        $experiment = 'test';
        $participant = '1';

        $hasFeatureFlag = PosthogExperimentsService::hasFeatureFlag($experiment, 'test_a', $participant);

        $this->assertTrue($hasFeatureFlag);
    }
}
