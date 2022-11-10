<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\Tests;

use CarAndClassic\PosthogExperiments\PosthogExperiments;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExampleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'input_cta_change',
                ],
            ], 200),
        ]);
    }

    public function testItCanGetAFeatureFlag(): void
    {
        $experiment = 'test';
        $participant = '1';

        $featureFlag = PosthogExperiments::getFeatureFlag($experiment, $participant);

        $this->assertSame('input_cta_change', $featureFlag);
        $this->assertTrue(Cache::has($experiment.md5($participant)));
    }
}
