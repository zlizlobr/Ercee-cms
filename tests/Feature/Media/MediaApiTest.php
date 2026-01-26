<?php

namespace Tests\Feature\Media;

use App\Domain\Media\MediaManifestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    private string $manifestPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manifestPath = public_path('media-manifest.json');
    }

    protected function tearDown(): void
    {
        if (File::exists($this->manifestPath)) {
            File::delete($this->manifestPath);
        }

        app(MediaManifestService::class)->clearCache();

        parent::tearDown();
    }

    private function createManifest(array $data): void
    {
        File::put($this->manifestPath, json_encode($data, JSON_PRETTY_PRINT));
        app(MediaManifestService::class)->clearCache();
    }

    public function test_index_returns_empty_array_when_no_manifest(): void
    {
        $response = $this->getJson('/api/v1/media');

        $response->assertStatus(200)
            ->assertJsonPath('data', []);
    }

    public function test_index_returns_all_media_from_manifest(): void
    {
        $this->createManifest([
            'uuid-1' => [
                'id' => 1,
                'uuid' => 'uuid-1',
                'original' => [
                    'url' => '/media/uuid-1/image.jpg',
                    'width' => 800,
                    'height' => 600,
                    'size' => 12345,
                    'mime' => 'image/jpeg',
                ],
                'variants' => [],
                'alt' => 'Test image',
                'title' => 'Test',
                'focal_point' => ['x' => 50, 'y' => 50],
                'tags' => ['test'],
                'checksum' => 'abc123',
                'exported_at' => '2026-01-23T00:00:00+00:00',
            ],
        ]);

        $response = $this->getJson('/api/v1/media');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.uuid', 'uuid-1')
            ->assertJsonPath('data.0.url', '/media/uuid-1/image.jpg')
            ->assertJsonPath('data.0.alt', 'Test image');
    }

    public function test_show_returns_single_media_by_uuid(): void
    {
        $this->createManifest([
            'uuid-1' => [
                'id' => 1,
                'uuid' => 'uuid-1',
                'original' => [
                    'url' => '/media/uuid-1/image.jpg',
                    'width' => 800,
                    'height' => 600,
                    'size' => 12345,
                    'mime' => 'image/jpeg',
                ],
                'variants' => [
                    'thumb' => [
                        'url' => '/media/uuid-1/conversions/image-thumb.jpg',
                        'width' => 150,
                        'height' => 150,
                        'size' => 5000,
                    ],
                ],
                'alt' => 'Test image',
                'title' => 'Test',
                'focal_point' => null,
                'tags' => [],
                'checksum' => 'abc123',
                'exported_at' => '2026-01-23T00:00:00+00:00',
            ],
        ]);

        $response = $this->getJson('/api/v1/media/uuid-1');

        $response->assertStatus(200)
            ->assertJsonPath('data.uuid', 'uuid-1')
            ->assertJsonPath('data.url', '/media/uuid-1/image.jpg')
            ->assertJsonPath('data.variants.thumb.url', '/media/uuid-1/conversions/image-thumb.jpg');
    }

    public function test_show_returns_404_for_nonexistent_uuid(): void
    {
        $this->createManifest([]);

        $response = $this->getJson('/api/v1/media/nonexistent');

        $response->assertStatus(404)
            ->assertJsonPath('error', 'Media not found');
    }

    public function test_resolve_returns_media_by_ids(): void
    {
        $this->createManifest([
            'uuid-1' => [
                'id' => 1,
                'uuid' => 'uuid-1',
                'original' => ['url' => '/media/uuid-1/a.jpg', 'width' => 100, 'height' => 100, 'size' => 1000, 'mime' => 'image/jpeg'],
                'variants' => [],
                'alt' => 'Image 1',
                'title' => 'Title 1',
                'focal_point' => null,
                'tags' => [],
                'checksum' => 'a',
                'exported_at' => '2026-01-23T00:00:00+00:00',
            ],
            'uuid-2' => [
                'id' => 2,
                'uuid' => 'uuid-2',
                'original' => ['url' => '/media/uuid-2/b.jpg', 'width' => 200, 'height' => 200, 'size' => 2000, 'mime' => 'image/jpeg'],
                'variants' => [],
                'alt' => 'Image 2',
                'title' => 'Title 2',
                'focal_point' => null,
                'tags' => [],
                'checksum' => 'b',
                'exported_at' => '2026-01-23T00:00:00+00:00',
            ],
        ]);

        $response = $this->postJson('/api/v1/media/resolve', [
            'ids' => [1, 2],
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.1.uuid', 'uuid-1')
            ->assertJsonPath('data.2.uuid', 'uuid-2');
    }

    public function test_resolve_validates_input(): void
    {
        $response = $this->postJson('/api/v1/media/resolve', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ids']);
    }
}
