<?php

declare(strict_types=1);

namespace CarAndClassic\PosthogExperiments\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class ExperimentViewComponentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Queue::fake();
    }

    public function testItRendersTheCorrectVariantHtml()
    {
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'test_a',
                ],
            ], 200),
        ]);

        $view = $this->blade(
            '<x-posthog-experiment experiment="test" participant="1">
                <x-slot name="control">
                    Control
                </x-slot>

                <x-slot name="test_a">
                    Test A
                </x-slot>

                <x-slot name="test_b">
                    Test B
                </x-slot>

                Fallback
            </x-posthog-experiment>'
        );

        $view->assertSee('Test A');
    }

    public function testItFallsBackToGenericSlotWhenSet()
    {
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'test_c',
                ],
            ], 200),
        ]);

        $view = $this->blade(
            '<x-posthog-experiment experiment="test" participant="1">
                <x-slot name="control">
                    Control
                </x-slot>

                <x-slot name="test_a">
                    Test A
                </x-slot>

                <x-slot name="test_b">
                    Test B
                </x-slot>

                Fallback
            </x-posthog-experiment>'
        );

        $view->assertSee('Fallback');
    }

    public function testItFallsBackToTheControlIfAGenericSlotIsNotSet()
    {
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'test_c',
                ],
            ], 200),
        ]);

        $view = $this->blade(
            '<x-posthog-experiment experiment="test" participant="1">
                <x-slot name="control">
                    Control
                </x-slot>

                <x-slot name="test_a">
                    Test A
                </x-slot>

                <x-slot name="test_b">
                    Test B
                </x-slot>
            </x-posthog-experiment>'
        );

        $view->assertSee('Control');
    }
    
    public function testItFallsBackToTheSpecifiedSlotIfAGenericSlotIsNotSet()
    {
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'test_c',
                ],
            ], 200),
        ]);

        $view = $this->blade(
            '<x-posthog-experiment experiment="test" participant="1" fallback="test_a">
                <x-slot name="control">
                    Control
                </x-slot>

                <x-slot name="test_a">
                    Test A
                </x-slot>

                <x-slot name="test_b">
                    Test B
                </x-slot>
            </x-posthog-experiment>'
        );

        $view->assertDontSee('Control');
        $view->assertSee('Test A');
        $view->assertDontSee('Test B');
    }

    public function testItFallsBackToAnEmptyStringIfThereIsNoControlOrGenericSlotSet()
    {
        Http::fake([
            '*' => Http::response([
                'featureFlags' => [
                    'test' => 'test_c',
                ],
            ], 200),
        ]);

        $view = $this->blade(
            '<x-posthog-experiment experiment="test" participant="1">
                <x-slot name="test_a">
                    Test A
                </x-slot>

                <x-slot name="test_b">
                    Test B
                </x-slot>
            </x-posthog-experiment>'
        );

        $view->assertSee('');
        $view->assertDontSee('Test A');
        $view->assertDontSee('Test B');
    }
}
