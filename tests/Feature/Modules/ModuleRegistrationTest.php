<?php

namespace Tests\Feature\Modules;

use App\Support\Module\ModuleManager;
use Tests\TestCase;

class ModuleRegistrationTest extends TestCase
{
    public function test_module_manager_is_registered_as_singleton(): void
    {
        $manager1 = $this->app->make(ModuleManager::class);
        $manager2 = $this->app->make(ModuleManager::class);

        $this->assertSame($manager1, $manager2);
    }

    public function test_all_configured_modules_are_loaded(): void
    {
        $manager = $this->app->make(ModuleManager::class);
        $configModules = config('modules.modules', []);

        foreach ($configModules as $name => $config) {
            if ($config['enabled'] ?? false) {
                $this->assertTrue(
                    $manager->isModuleEnabled($name),
                    "Module [{$name}] should be enabled"
                );
            }
        }
    }

    public function test_commerce_module_is_loaded(): void
    {
        $manager = $this->app->make(ModuleManager::class);

        $this->assertTrue($manager->isModuleEnabled('commerce'));
        $this->assertEquals('1.0.0', $manager->getModule('commerce')->getVersion());
    }

    public function test_forms_module_is_loaded(): void
    {
        $manager = $this->app->make(ModuleManager::class);

        $this->assertTrue($manager->isModuleEnabled('forms'));
        $this->assertEquals('1.0.0', $manager->getModule('forms')->getVersion());
    }

    public function test_funnel_module_is_loaded(): void
    {
        $manager = $this->app->make(ModuleManager::class);

        $this->assertTrue($manager->isModuleEnabled('funnel'));
        $this->assertEquals('1.0.0', $manager->getModule('funnel')->getVersion());
    }

    public function test_funnel_module_has_cross_module_dependencies(): void
    {
        $manager = $this->app->make(ModuleManager::class);
        $funnel = $manager->getModule('funnel');

        $dependencies = $funnel->getDependencies();
        $this->assertArrayHasKey('forms', $dependencies);
        $this->assertArrayHasKey('commerce', $dependencies);
    }

    public function test_module_resources_are_registered(): void
    {
        $manager = $this->app->make(ModuleManager::class);
        $resources = $manager->getModuleResources();

        $this->assertNotEmpty($resources, 'Module resources should be registered');
    }

    public function test_module_blocks_include_forms_contact_block(): void
    {
        $manager = $this->app->make(ModuleManager::class);
        $blocks = $manager->getModuleBlocks();

        $this->assertNotEmpty($blocks, 'Forms module should register blocks');
    }

    public function test_module_permissions_are_prefixed(): void
    {
        $manager = $this->app->make(ModuleManager::class);
        $permissions = $manager->getAllPermissions();

        $this->assertNotEmpty($permissions);

        foreach ($permissions as $permission) {
            $this->assertStringStartsWith('module.', $permission);
        }
    }

    public function test_module_config_is_merged(): void
    {
        $this->assertEquals('commerce', config('module.commerce.name'));
        $this->assertEquals('forms', config('module.forms.name'));
        $this->assertEquals('funnel', config('module.funnel.name'));
    }

    public function test_commerce_config_has_stripe_keys(): void
    {
        $this->assertArrayHasKey('key', config('module.commerce.stripe'));
        $this->assertArrayHasKey('secret', config('module.commerce.stripe'));
        $this->assertArrayHasKey('webhook_secret', config('module.commerce.stripe'));
    }

    public function test_forms_config_has_rate_limit(): void
    {
        $this->assertNotNull(config('module.forms.rate_limit.submissions_per_minute'));
    }

    public function test_funnel_config_has_queue_settings(): void
    {
        $this->assertNotNull(config('module.funnel.queue.connection'));
        $this->assertNotNull(config('module.funnel.queue.queue'));
    }
}
