<?php

namespace Tests\Unit\Application\Content;

use App\Application\Content\Commands\PublishPageCommand;
use App\Application\Content\PublishPageHandler;
use App\Domain\Content\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishPageHandlerTest extends TestCase
{
    use RefreshDatabase;

    private PublishPageHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new PublishPageHandler;
    }

    public function test_returns_page_not_found_when_page_does_not_exist(): void
    {
        $command = new PublishPageCommand(pageId: 999);

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Page not found', $result->error);
    }

    public function test_returns_already_published_when_page_is_published(): void
    {
        $page = Page::factory()->create([
            'status' => Page::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $command = new PublishPageCommand(pageId: $page->id);

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Page is already published', $result->error);
    }

    public function test_returns_validation_failed_when_title_is_empty(): void
    {
        $page = Page::factory()->create([
            'title' => '',
            'slug' => 'test-page',
            'status' => Page::STATUS_DRAFT,
        ]);

        $command = new PublishPageCommand(pageId: $page->id);

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Page title is required', $result->error);
    }

    public function test_returns_validation_failed_when_slug_is_empty(): void
    {
        $page = Page::factory()->create([
            'title' => 'Test Page',
            'slug' => '',
            'status' => Page::STATUS_DRAFT,
        ]);

        $command = new PublishPageCommand(pageId: $page->id);

        $result = $this->handler->handle($command);

        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Page slug is required', $result->error);
    }

    public function test_publishes_page_successfully(): void
    {
        $page = Page::factory()->create([
            'title' => 'Test Page',
            'slug' => 'test-page',
            'status' => Page::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $command = new PublishPageCommand(pageId: $page->id);

        $result = $this->handler->handle($command);

        $this->assertTrue($result->isSuccess());
        $this->assertNotNull($result->publishedAt);

        $page->refresh();
        $this->assertEquals(Page::STATUS_PUBLISHED, $page->status);
        $this->assertNotNull($page->published_at);
    }
}
