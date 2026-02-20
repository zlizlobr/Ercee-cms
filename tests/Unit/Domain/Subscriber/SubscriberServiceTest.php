<?php

namespace Tests\Unit\Domain\Subscriber;

use App\Domain\Subscriber\Subscriber;
use App\Domain\Subscriber\SubscriberRepository;
use App\Domain\Subscriber\SubscriberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SubscriberServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubscriberRepository $repository;

    private SubscriberService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(SubscriberRepository::class);
        $this->service = new SubscriberService($this->repository);
    }

    public function test_find_or_create_returns_existing_subscriber_when_found(): void
    {
        $existing = Subscriber::factory()->create(['email' => 'existing@example.com']);

        $this->repository
            ->shouldReceive('findByEmail')
            ->with('existing@example.com')
            ->once()
            ->andReturn($existing);

        $this->repository->shouldNotReceive('create');

        $result = $this->service->findOrCreate('existing@example.com');

        $this->assertSame($existing, $result);
    }

    public function test_find_or_create_creates_new_subscriber_when_not_found(): void
    {
        $created = Subscriber::factory()->make([
            'email' => 'new@example.com',
            'status' => 'active',
        ]);

        $this->repository
            ->shouldReceive('findByEmail')
            ->with('new@example.com')
            ->once()
            ->andReturn(null);

        $this->repository
            ->shouldReceive('create')
            ->with(['email' => 'new@example.com', 'status' => 'active'])
            ->once()
            ->andReturn($created);

        $result = $this->service->findOrCreate('new@example.com');

        $this->assertSame($created, $result);
    }

    public function test_find_or_create_merges_extra_data_on_create(): void
    {
        $created = Subscriber::factory()->make([
            'email' => 'new@example.com',
            'status' => 'active',
            'source' => 'checkout',
        ]);

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->andReturn(null);

        $this->repository
            ->shouldReceive('create')
            ->with(['email' => 'new@example.com', 'status' => 'active', 'source' => 'checkout'])
            ->once()
            ->andReturn($created);

        $result = $this->service->findOrCreate('new@example.com', ['source' => 'checkout']);

        $this->assertSame($created, $result);
    }

    public function test_find_or_create_does_not_override_status_with_default_when_provided(): void
    {
        $created = Subscriber::factory()->make(['email' => 'x@example.com', 'status' => 'pending']);

        $this->repository->shouldReceive('findByEmail')->once()->andReturn(null);

        $this->repository
            ->shouldReceive('create')
            ->withArgs(function (array $data) {
                // extra data 'status' => 'pending' should override the default 'active'
                return $data['email'] === 'x@example.com' && $data['status'] === 'pending';
            })
            ->once()
            ->andReturn($created);

        $this->service->findOrCreate('x@example.com', ['status' => 'pending']);
    }

    public function test_add_tags_calls_add_tag_on_subscriber_for_each_tag(): void
    {
        $subscriber = Subscriber::factory()->create();

        $this->service->addTags($subscriber, ['newsletter', 'vip']);

        $this->assertDatabaseHas('subscriber_tags', [
            'subscriber_id' => $subscriber->id,
            'tag' => 'newsletter',
        ]);
        $this->assertDatabaseHas('subscriber_tags', [
            'subscriber_id' => $subscriber->id,
            'tag' => 'vip',
        ]);
    }

    public function test_add_tags_with_empty_array_does_nothing(): void
    {
        $subscriber = Subscriber::factory()->create();

        $this->service->addTags($subscriber, []);

        $this->assertDatabaseMissing('subscriber_tags', [
            'subscriber_id' => $subscriber->id,
        ]);
    }

    public function test_remove_tags_removes_specified_tags(): void
    {
        $subscriber = Subscriber::factory()->create();
        $subscriber->addTag('newsletter');
        $subscriber->addTag('vip');

        $this->service->removeTags($subscriber, ['newsletter']);

        $this->assertDatabaseMissing('subscriber_tags', [
            'subscriber_id' => $subscriber->id,
            'tag' => 'newsletter',
        ]);
        $this->assertDatabaseHas('subscriber_tags', [
            'subscriber_id' => $subscriber->id,
            'tag' => 'vip',
        ]);
    }

    public function test_remove_tags_with_empty_array_does_nothing(): void
    {
        $subscriber = Subscriber::factory()->create();
        $subscriber->addTag('newsletter');

        $this->service->removeTags($subscriber, []);

        $this->assertDatabaseHas('subscriber_tags', [
            'subscriber_id' => $subscriber->id,
            'tag' => 'newsletter',
        ]);
    }

    public function test_has_tag_returns_true_when_tag_exists(): void
    {
        $subscriber = Subscriber::factory()->create();
        $subscriber->addTag('newsletter');

        $result = $this->service->hasTag($subscriber, 'newsletter');

        $this->assertTrue($result);
    }

    public function test_has_tag_returns_false_when_tag_does_not_exist(): void
    {
        $subscriber = Subscriber::factory()->create();

        $result = $this->service->hasTag($subscriber, 'newsletter');

        $this->assertFalse($result);
    }
}
