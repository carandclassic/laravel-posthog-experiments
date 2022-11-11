<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class HasFeatureFlagBladeDirectiveTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Queue::fake();
    }

    public function testItRendersTheCorrectVariantHtml(): void
    {
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'test_a',
                ],
            ], 200),
        ]);

        $view = $this->blade(
            '@hasFeatureFlag(\'test\', \'control\', 1)
                Control
            @elsehasFeatureFlag(\'test\', \'test_a\', 1)
                Test A
            @elsehasFeatureFlag(\'test\', \'test_b\', 1)
                Test B
            @else
                Fallback
            @endhasFeatureFlag'
        );

        $view->assertSee('Test A');
    }

    public function testItFallsBackToElseWhenNotMatched(): void
    {
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'test_c',
                ],
            ], 200),
        ]);

        $view = $this->blade(
            '@hasFeatureFlag(\'test\', \'control\', 1)
                Control
            @elsehasFeatureFlag(\'test\', \'test_a\', 1)
                Test A
            @elsehasFeatureFlag(\'test\', \'test_b\', 1)
                Test B
            @else
                Fallback
            @endhasFeatureFlag'
        );

        $view->assertSee('Fallback');
    }
}
