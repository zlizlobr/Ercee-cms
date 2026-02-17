<?php

namespace Tests\Unit\Support\Module;

use App\Support\Module\ModuleManager;
use Tests\TestCase;

class ModuleManagerTest extends TestCase
{
    private ModuleManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = new ModuleManager($this->app);
    }

    public function test_loads_enabled_module_from_config(): void
    {
        $this->manager->loadModule('forms', [
            'enabled' => true,
            'provider' => \Modules\Forms\FormsModuleServiceProvider::class,
            'version' => '1.0.0',
        ]);

        $this->assertTrue($this->manager->isModuleEnabled('forms'));
        $this->assertNotNull($this->manager->getModule('forms'));
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
        $this->manager->loadModule('forms', [
            'enabled' => true,
            'provider' => \Modules\Forms\FormsModuleServiceProvider::class,
            'version' => '1.0.0',
        ]);

        $this->manager->loadModule('commerce', [
            'enabled' => true,
            'provider' => \Modules\Commerce\CommerceModuleServiceProvider::class,
            'version' => '1.0.0',
        ]);

        $modules = $this->manager->getModules();

        $this->assertCount(2, $modules);
        $this->assertArrayHasKey('forms', $modules);
        $this->assertArrayHasKey('commerce', $modules);
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
}
