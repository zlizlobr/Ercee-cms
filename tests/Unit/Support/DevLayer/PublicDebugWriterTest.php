<?php

namespace Tests\Unit\Support\DevLayer;

use App\Support\DevLayer\ErceeDevLayerPolicy;
use App\Support\DevLayer\PublicDebugWriter;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PublicDebugWriterTest extends TestCase
{
    private string $debugDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->debugDir = public_path('debug');
        if (File::exists($this->debugDir)) {
            File::deleteDirectory($this->debugDir);
        }
    }

    protected function tearDown(): void
    {
        if (File::exists($this->debugDir)) {
            File::deleteDirectory($this->debugDir);
        }

        parent::tearDown();
    }

    public function test_writer_skips_output_when_public_debug_is_disabled(): void
    {
        $policy = new ErceeDevLayerPolicy([
            'public_debug_enabled' => false,
        ]);
        $writer = new PublicDebugWriter($policy);

        $result = $writer->writeJson('snapshot.json', ['x' => 1]);

        $this->assertFalse($result);
        $this->assertFileDoesNotExist(public_path('debug/snapshot.json'));
    }

    public function test_writer_creates_json_file_when_public_debug_is_enabled(): void
    {
        $policy = new ErceeDevLayerPolicy([
            'public_debug_enabled' => true,
        ]);
        $writer = new PublicDebugWriter($policy);

        $result = $writer->writeJson('feature/state.json', ['ok' => true]);

        $this->assertTrue($result);
        $this->assertFileExists(public_path('debug/feature/state.json'));
        $this->assertStringContainsString('"ok": true', (string) File::get(public_path('debug/feature/state.json')));
    }

    public function test_writer_rejects_path_traversal_attempts(): void
    {
        $policy = new ErceeDevLayerPolicy([
            'public_debug_enabled' => true,
        ]);
        $writer = new PublicDebugWriter($policy);

        $result = $writer->writeJson('../escape.json', ['x' => 1]);

        $this->assertFalse($result);
        $this->assertFileDoesNotExist(public_path('debug/../escape.json'));
    }
}
