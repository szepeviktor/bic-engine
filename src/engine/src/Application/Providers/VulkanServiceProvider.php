<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Providers;

use Bic\Application;
use Bic\Application\Extension\Repository as ExtensionsRepository;
use Bic\Application\Initializer;
use Bic\Application\InitializerInterface;
use Bic\Application\Layer\Repository as LayersRepository;
use Bic\Type\Version;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Support\ServiceProvider;
use Serafim\Vulkan\VkInstance;
use Serafim\Vulkan\Vulkan;

/**
 * @property-read Application $app
 */
class VulkanServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    public const LOCATOR_API = 'vk';

    /**
     * @var string
     */
    public const LOCATOR_INITIALIZER = 'vk.initializer';

    /**
     * @var string
     */
    public const LOCATOR_EXTENSIONS = 'vk.extensions';

    /**
     * @var string
     */
    public const LOCATOR_LAYERS = 'vk.layers';

    /**
     * @var string
     */
    public const LOCATOR_INSTANCE = 'vk.instance';

    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerVulkan();
        $this->registerRepositories();
        $this->registerInitializer();
        $this->registerVkInstance();
    }

    /**
     * @return void
     */
    private function registerVulkan(): void
    {
        if (! $this->app->bound(Vulkan::class)) {
            $this->app->singleton(Vulkan::class, function () {
                /** @var string $version */
                $version = $this->app->make(RepositoryContract::class)
                    ->get('vulkan.version', '1.0.0')
                ;

                return new Vulkan(Version::fromString($version)->toInt());
            });
        }

        $this->app->alias(Vulkan::class, static::LOCATOR_API);
    }

    /**
     * @return void
     */
    private function registerRepositories(): void
    {
        if (! $this->app->bound(ExtensionsRepository::class)) {
            $this->app->singleton(ExtensionsRepository::class);
        }

        if (! $this->app->bound(LayersRepository::class)) {
            $this->app->singleton(LayersRepository::class);
        }

        $this->app->alias(ExtensionsRepository::class, static::LOCATOR_EXTENSIONS);
        $this->app->alias(LayersRepository::class, static::LOCATOR_LAYERS);
    }

    /**
     * @return void
     */
    private function registerInitializer(): void
    {
        if (! $this->app->bound(InitializerInterface::class)) {
            $this->app->singleton(InitializerInterface::class, function () {
                $config = $this->app->make(RepositoryContract::class);

                $initializer = new Initializer(
                    $this->app->make(Vulkan::class),
                    $this->app->make(ExtensionsRepository::class),
                    $this->app->make(LayersRepository::class),
                );

                $initializer->addLayers(
                    \array_filter($config->get('vulkan.layers.required', []))
                );

                $initializer->addOptionalLayers(
                    \array_filter($config->get('vulkan.layers.optional', []))
                );

                $initializer->addExtensions(
                    \array_filter($config->get('vulkan.extensions.required', []))
                );

                $initializer->addOptionalExtensions(
                    \array_filter($config->get('vulkan.extensions.optional', []))
                );

                return $initializer->with([
                    // Application Info
                    'pApplicationName'   => $config->get('app.name', 'Application'),
                    'applicationVersion' => Version::fromString(
                        $config->get('app.version', '1.0.0')
                    ),

                    // Engine Info
                    'pEngineName'        => Application::ENGINE_NAME,
                    'engineVersion'      => Version::fromString(Application::ENGINE_VERSION),

                    // Vulkan Info
                    'apiVersion'         => Version::fromString(
                        $config->get('vulkan.version', '1.0.0')
                    ),
                ]);
            });
        }

        $this->app->alias(InitializerInterface::class, static::LOCATOR_INITIALIZER);
    }

    /**
     * @return void
     */
    private function registerVkInstance(): void
    {
        if (! $this->app->bound(VkInstance::class)) {
            $this->app->singleton(VkInstance::class, function () {
                $initializer = $this->app->make(InitializerInterface::class);

                return $initializer->create();
            });
        }

        $this->app->alias(InitializerInterface::class, static::LOCATOR_INSTANCE);
    }
}
