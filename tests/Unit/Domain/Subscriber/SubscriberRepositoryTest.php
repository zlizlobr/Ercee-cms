<?php

namespace Tests\Unit\Domain\Subscriber;

use App\Domain\Subscriber\Subscriber;
use App\Domain\Subscriber\SubscriberRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SubscriberRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new SubscriberRepository();
    }

    public function test_find_by_email_returns_subscriber_when_exists(): void
    {
        Subscriber::factory()->create(['email' => 'found@example.com']);

        $result = $this->repository->findByEmail('found@example.com');

        $this->assertInstanceOf(Subscriber::class, $result);
        $this->assertEquals('found@example.com', $result->email);
    }

    public function test_find_by_email_returns_null_when_not_exists(): void
    {
        $result = $this->repository->findByEmail('missing@example.com');

        $this->assertNull($result);
    }

    public function test_create_persists_subscriber_with_given_data(): void
    {
        $result = $this->repository->create([
            'email' => 'new@example.com',
            'status' => 'active',
            'source' => 'api',
        ]);

        $this->assertInstanceOf(Subscriber::class, $result);
        $this->assertNotNull($result->id);
        $this->assertEquals('new@example.com', $result->email);
        $this->assertEquals('active', $result->status);
        $this->assertEquals('api', $result->source);

        $this->assertDatabaseHas('subscribers', [
            'email' => 'new@example.com',
            'status' => 'active',
            'source' => 'api',
        ]);
    }

    public function test_update_or_create_creates_new_subscriber_when_not_exists(): void
    {
        $result = $this->repository->updateOrCreate('created@example.com', [
            'status' => 'active',
            'source' => 'form',
        ]);

        $this->assertInstanceOf(Subscriber::class, $result);
        $this->assertEquals('created@example.com', $result->email);
        $this->assertEquals('active', $result->status);
        $this->assertDatabaseCount('subscribers', 1);
    }

    public function test_update_or_create_updates_existing_subscriber(): void
    {
        Subscriber::factory()->create([
            'email' => 'existing@example.com',
            'status' => 'inactive',
            'source' => 'old',
        ]);

        $result = $this->repository->updateOrCreate('existing@example.com', [
            'status' => 'active',
            'source' => 'new',
        ]);

        $this->assertEquals('existing@example.com', $result->email);
        $this->assertEquals('active', $result->status);
        $this->assertEquals('new', $result->source);
        $this->assertDatabaseCount('subscribers', 1);

        $this->assertDatabaseHas('subscribers', [
            'email' => 'existing@example.com',
            'status' => 'active',
            'source' => 'new',
        ]);
    }
}
