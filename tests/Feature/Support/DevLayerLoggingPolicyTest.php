<?php

namespace Tests\Feature\Support;

use App\Support\DevLayer\ErceeDevLayerPolicy;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DevLayerLoggingPolicyTest extends TestCase
{
    private string $logFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logFile = storage_path('logs/dev-layer-policy.log');
        if (File::exists($this->logFile)) {
            File::delete($this->logFile);
        }
    }

    protected function tearDown(): void
    {
        if (File::exists($this->logFile)) {
            File::delete($this->logFile);
        }

        parent::tearDown();
    }

    public function test_debug_logs_are_written_in_dev_profile(): void
    {
        $resolved = ErceeDevLayerPolicy::resolve([
            'APP_ENV' => 'local',
            'ERCEE_RUNTIME_PROFILE' => 'dev',
            'ERCEE_DEV_LAYER' => 'true',
            'ERCEE_LOG_LEVEL' => 'debug',
        ]);

        $this->configureSingleChannel($resolved['log_level']);
        Log::channel('single')->debug('dev-debug-visible');

        $this->assertFileExists($this->logFile);
        $contents = (string) File::get($this->logFile);
        $this->assertStringContainsString('dev-debug-visible', $contents);
    }

    public function test_debug_logs_are_filtered_out_in_prod_profile_while_info_remains(): void
    {
        $resolved = ErceeDevLayerPolicy::resolve([
            'APP_ENV' => 'production',
            'ERCEE_RUNTIME_PROFILE' => 'prod',
            'ERCEE_DEV_LAYER' => 'true',
            'ERCEE_LOG_LEVEL' => 'debug',
        ]);

        $this->configureSingleChannel($resolved['log_level']);
        Log::channel('single')->debug('prod-debug-hidden');
        Log::channel('single')->info('prod-info-visible');

        $this->assertFileExists($this->logFile);
        $contents = (string) File::get($this->logFile);
        $this->assertStringNotContainsString('prod-debug-hidden', $contents);
        $this->assertStringContainsString('prod-info-visible', $contents);
    }

    private function configureSingleChannel(string $level): void
    {
        config()->set('logging.default', 'single');
        config()->set('logging.channels.single.path', $this->logFile);
        config()->set('logging.channels.single.level', $level);

        app('log')->forgetChannel('single');
    }
}
