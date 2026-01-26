<?php

namespace Tests\Feature\Media;

use App\Domain\Media\MediaLibrary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaExportCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $publicMediaPath;
    private string $manifestPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicMediaPath = public_path('media');
        $this->manifestPath = public_path('media-manifest.json');

        Storage::fake('media');

        if (File::isDirectory($this->publicMediaPath)) {
            File::deleteDirectory($this->publicMediaPath);
        }

        if (File::exists($this->manifestPath)) {
            File::delete($this->manifestPath);
        }
    }

    protected function tearDown(): void
    {
        if (File::isDirectory($this->publicMediaPath)) {
            File::deleteDirectory($this->publicMediaPath);
        }

        if (File::exists($this->manifestPath)) {
            File::delete($this->manifestPath);
        }

        parent::tearDown();
    }

    public function test_exports_media_and_creates_manifest(): void
    {
        $mediaItem = MediaLibrary::create([
            'title' => 'Test Image',
            'alt_text' => 'A test image',
            'focal_point' => ['x' => 50, 'y' => 50],
            'tags' => ['test', 'image'],
        ]);

        $file = UploadedFile::fake()->image('test.jpg', 800, 600);
        $mediaItem->addMedia($file)->toMediaCollection('default');

        $this->artisan('media:export')
            ->assertSuccessful();

        $this->assertFileExists($this->manifestPath);

        $manifest = json_decode(File::get($this->manifestPath), true);
        $this->assertNotEmpty($manifest);

        $uuid = $mediaItem->getFirstMedia('default')->uuid;
        $this->assertArrayHasKey($uuid, $manifest);

        $entry = $manifest[$uuid];
        $this->assertEquals($mediaItem->id, $entry['id']);
        $this->assertEquals('A test image', $entry['alt']);
        $this->assertEquals('Test Image', $entry['title']);
        $this->assertArrayHasKey('original', $entry);
        $this->assertArrayHasKey('variants', $entry);
    }

    public function test_manifest_structure_is_correct(): void
    {
        $mediaItem = MediaLibrary::create([
            'title' => 'Structure Test',
            'alt_text' => 'Alt text',
        ]);

        $file = UploadedFile::fake()->image('structure.jpg', 1200, 800);
        $mediaItem->addMedia($file)->toMediaCollection('default');

        $this->artisan('media:export')->assertSuccessful();

        $manifest = json_decode(File::get($this->manifestPath), true);
        $uuid = $mediaItem->getFirstMedia('default')->uuid;
        $entry = $manifest[$uuid];

        $this->assertArrayHasKey('id', $entry);
        $this->assertArrayHasKey('uuid', $entry);
        $this->assertArrayHasKey('original', $entry);
        $this->assertArrayHasKey('variants', $entry);
        $this->assertArrayHasKey('alt', $entry);
        $this->assertArrayHasKey('title', $entry);
        $this->assertArrayHasKey('focal_point', $entry);
        $this->assertArrayHasKey('tags', $entry);
        $this->assertArrayHasKey('checksum', $entry);
        $this->assertArrayHasKey('exported_at', $entry);

        $this->assertArrayHasKey('url', $entry['original']);
        $this->assertArrayHasKey('width', $entry['original']);
        $this->assertArrayHasKey('height', $entry['original']);
        $this->assertArrayHasKey('size', $entry['original']);
        $this->assertArrayHasKey('mime', $entry['original']);
    }

    public function test_only_changed_flag_skips_unchanged_media(): void
    {
        $mediaItem = MediaLibrary::create(['title' => 'Unchanged Test']);

        $file = UploadedFile::fake()->image('unchanged.jpg', 400, 300);
        $mediaItem->addMedia($file)->toMediaCollection('default');

        $this->artisan('media:export')->assertSuccessful();

        $manifestBefore = json_decode(File::get($this->manifestPath), true);
        $uuid = $mediaItem->getFirstMedia('default')->uuid;
        $exportedAtBefore = $manifestBefore[$uuid]['exported_at'];

        sleep(1);

        $this->artisan('media:export', ['--only-changed' => true])
            ->assertSuccessful();

        $manifestAfter = json_decode(File::get($this->manifestPath), true);
        $exportedAtAfter = $manifestAfter[$uuid]['exported_at'];

        $this->assertEquals($exportedAtBefore, $exportedAtAfter);
    }

    public function test_exports_media_files_to_public_directory(): void
    {
        $mediaItem = MediaLibrary::create(['title' => 'File Export Test']);

        $file = UploadedFile::fake()->image('export.jpg', 600, 400);
        $mediaItem->addMedia($file)->toMediaCollection('default');

        $this->artisan('media:export')->assertSuccessful();

        $uuid = $mediaItem->getFirstMedia('default')->uuid;
        $targetDir = "{$this->publicMediaPath}/{$uuid}";

        $this->assertDirectoryExists($targetDir);
    }

    public function test_handles_empty_media_library(): void
    {
        $this->artisan('media:export')
            ->assertSuccessful();

        $this->assertFileExists($this->manifestPath);

        $manifest = json_decode(File::get($this->manifestPath), true);
        $this->assertEmpty($manifest);
    }

    public function test_cleans_up_orphaned_directories(): void
    {
        $orphanedDir = "{$this->publicMediaPath}/orphaned-uuid";
        File::makeDirectory($orphanedDir, 0755, true);
        File::put("{$orphanedDir}/test.txt", 'test');

        $this->artisan('media:export')->assertSuccessful();

        $this->assertDirectoryDoesNotExist($orphanedDir);
    }
}
