<?php

namespace Tests\Unit\Support\Module;

use App\Contracts\Module\HasEventsInterface;
use App\Contracts\Module\HasMigrationsInterface;
use App\Contracts\Module\HasRoutesInterface;
use App\Contracts\Module\ModuleInterface;
use App\Support\Module\ModuleManager;
use Illuminate\Foundation\Application;
use Mockery;
use PHPUnit\Framework\TestCase;

class ModuleManagerTest extends TestCase
{
    private ModuleManager $manager;

    private Application $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = Mockery::mock(Application::class);
        $this->manager = new ModuleManager($this->app);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_loads_enabled_module_from_config(): void
    {
        $provider = $this->createMockProvider('test', '1.0.0');

        $this->manager->loadModule('test', [
            'enabled' => true,
            'provider' => get_class($provider),
            'version' => '1.0.0',
        ]);

        $this->assertTrue($this->manager->isModuleEnabled('test'));
        $this->assertNotNull($this->manager->getModule('test'));
    }

    public function test_skips_module_with_missing_provider_class(): void
    {
        $this->manager->loadModule('missing', [
            'enabled' => true,
            'provider' => 'NonExistent\\Provider\\Class',
            'version' => '1.0.0',
        ]);

        $this->assertFalse($this->manager->isModuleEnabled('missing'));
    }

    public function test_returns_null_for_unregistered_module(): void
    {
        $this->assertNull($this->manager->getModule('nonexistent'));
        $this->assertFalse($this->manager->isModuleEnabled('nonexistent'));
    }

    public function test_get_modules_returns_all_loaded(): void
    {
        $provider1 = $this->createMockProvider('mod1', '1.0.0');
        $provider2 = $this->createMockProvider('mod2', '2.0.0');

        $this->manager->loadModule('mod1', [
            'enabled' => true,
            'provider' => get_class($provider1),
            'version' => '1.0.0',
        ]);
        $this->manager->loadModule('mod2', [
            'enabled' => true,
            'provider' => get_class($provider2),
            'version' => '2.0.0',
        ]);

        $modules = $this->manager->getModules();
        $this->assertCount(2, $modules);
    }

    public function test_caret_constraint_matches_compatible_versions(): void
    {
        $this->assertTrue($this->invokeMatchesConstraint('1.0.0', '^1.0'));
        $this->assertTrue($this->invokeMatchesConstraint('1.5.0', '^1.0'));
        $this->assertTrue($this->invokeMatchesConstraint('1.99.99', '^1.0'));
        $this->assertFalse($this->invokeMatchesConstraint('2.0.0', '^1.0'));
        $this->assertFalse($this->invokeMatchesConstraint('0.9.0', '^1.0'));
    }

    public function test_tilde_constraint_matches_patch_versions(): void
    {
        $this->assertTrue($this->invokeMatchesConstraint('1.2.0', '~1.2'));
        $this->assertTrue($this->invokeMatchesConstraint('1.2.5', '~1.2'));
        $this->assertFalse($this->invokeMatchesConstraint('1.3.0', '~1.2'));
        $this->assertFalse($this->invokeMatchesConstraint('1.1.0', '~1.2'));
    }

    public function test_gte_constraint(): void
    {
        $this->assertTrue($this->invokeMatchesConstraint('2.0.0', '>=1.0'));
        $this->assertTrue($this->invokeMatchesConstraint('1.0.0', '>=1.0'));
        $this->assertFalse($this->invokeMatchesConstraint('0.9.0', '>=1.0'));
    }

    public function test_gt_constraint(): void
    {
        $this->assertTrue($this->invokeMatchesConstraint('2.0.0', '>1.0'));
        $this->assertFalse($this->invokeMatchesConstraint('1.0.0', '>1.0'));
    }

    public function test_lte_constraint(): void
    {
        $this->assertTrue($this->invokeMatchesConstraint('1.0.0', '<=1.0'));
        $this->assertTrue($this->invokeMatchesConstraint('0.5.0', '<=1.0'));
        $this->assertFalse($this->invokeMatchesConstraint('1.1.0', '<=1.0'));
    }

    public function test_lt_constraint(): void
    {
        $this->assertTrue($this->invokeMatchesConstraint('0.9.0', '<1.0'));
        $this->assertFalse($this->invokeMatchesConstraint('1.0.0', '<1.0'));
    }

    public function test_wildcard_constraint_matches_any_version(): void
    {
        $this->assertTrue($this->invokeMatchesConstraint('0.0.1', '*'));
        $this->assertTrue($this->invokeMatchesConstraint('99.99.99', '*'));
    }

    public function test_exact_version_constraint(): void
    {
        $this->assertTrue($this->invokeMatchesConstraint('1.0.0', '1.0.0'));
        $this->assertTrue($this->invokeMatchesConstraint('1.1.0', '1.0.0'));
        $this->assertFalse($this->invokeMatchesConstraint('0.9.0', '1.0.0'));
    }

    private function invokeMatchesConstraint(string $version, string $constraint): bool
    {
        $reflection = new \ReflectionMethod($this->manager, 'matchesConstraint');
        $reflection->setAccessible(true);

        return $reflection->invoke($this->manager, $version, $constraint);
    }

    private function createMockProvider(string $name, string $version): ModuleInterface
    {
        $mock = Mockery::mock(ModuleInterface::class);
        $mock->shouldReceive('getName')->andReturn($name);
        $mock->shouldReceive('getVersion')->andReturn($version);
        $mock->shouldReceive('getDependencies')->andReturn([]);
        $mock->shouldReceive('getPermissions')->andReturn([]);

        return $mock;
    }
}
