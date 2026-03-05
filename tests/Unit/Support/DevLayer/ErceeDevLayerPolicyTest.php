<?php

namespace Tests\Unit\Support\DevLayer;

use App\Support\DevLayer\ErceeDevLayerPolicy;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ErceeDevLayerPolicyTest extends TestCase
{
    public function test_it_resolves_defaults_for_local_environment(): void
    {
        $resolved = ErceeDevLayerPolicy::resolve([
            'APP_ENV' => 'local',
        ]);

        $this->assertSame('dev', $resolved['runtime_profile']);
        $this->assertTrue($resolved['dev_layer_enabled']);
        $this->assertSame('debug', $resolved['log_level']);
        $this->assertTrue($resolved['can_write_debug_logs']);
        $this->assertFalse($resolved['public_debug_enabled']);
    }

    public function test_it_blocks_debug_level_for_production_profile(): void
    {
        $resolved = ErceeDevLayerPolicy::resolve([
            'APP_ENV' => 'production',
            'ERCEE_RUNTIME_PROFILE' => 'prod',
            'ERCEE_LOG_LEVEL' => 'debug',
            'ERCEE_PUBLIC_DEBUG' => 'true',
        ]);

        $this->assertSame('prod', $resolved['runtime_profile']);
        $this->assertSame('info', $resolved['log_level']);
        $this->assertFalse($resolved['can_write_debug_logs']);
        $this->assertFalse($resolved['public_debug_enabled']);
    }

    public function test_it_collects_invalid_values_and_falls_back_to_defaults(): void
    {
        $resolved = ErceeDevLayerPolicy::resolve([
            'APP_ENV' => 'staging',
            'ERCEE_RUNTIME_PROFILE' => 'invalid',
            'ERCEE_DEV_LAYER' => 'maybe',
            'ERCEE_LOG_LEVEL' => 'verbose',
            'ERCEE_PUBLIC_DEBUG' => 'sometimes',
        ]);

        $this->assertSame('staging', $resolved['runtime_profile']);
        $this->assertTrue($resolved['dev_layer_enabled']);
        $this->assertSame('info', $resolved['log_level']);
        $this->assertCount(4, $resolved['invalid_values']);
    }

    public function test_dev_debug_helper_emits_message_only_when_policy_allows_it(): void
    {
        config()->set('ercee_dev.can_write_debug_logs', false);
        Log::spy();

        dev_debug('debug-off');
        Log::shouldNotHaveReceived('debug');

        config()->set('ercee_dev.can_write_debug_logs', true);
        dev_debug('debug-on', ['a' => 1]);
        Log::shouldHaveReceived('debug')->once()->with('debug-on', ['a' => 1]);
    }
}
