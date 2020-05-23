<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Providers;

use Bic\Application\Extension\Repository as Extensions;
use Bic\Application\InitializerInterface;
use Bic\Application\Layer\Repository as Layers;
use Bic\Application\NamedTypeInterface;
use Bic\Application\Providers\VulkanDebugServiceProvider\VkExtDebugReport;
use Bic\Device\Physical\Device;
use Bic\Exception\InitializationException;
use Bic\Type\Collection;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Serafim\Vulkan\Enum\VkStructureType;
use Serafim\Vulkan\VkInstance;
use Serafim\Vulkan\Vulkan;

/**
 * Class VulkanDebugServiceProvider
 */
class VulkanDebugServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    private const DEBUG_LAYERS = [
        /**
         * The main, comprehensive Khronos validation layer.
         *
         * Vulkan is an Explicit API, enabling direct control over how
         * GPUs actually work. By design, minimal error checking is
         * done inside a Vulkan driver. Applications have full control
         * and responsibility for correct operation. Any errors in how
         * Vulkan is used can result in a crash. The Khronos Valiation
         * Layer can be enabled to assist development by enabling
         * developers to verify their applications correctly use the
         * Vulkan API.
         *
         * Note: Only available if the Vulkan SDK is installed.
         *
         * @see https://vulkan.lunarg.com/doc/sdk/1.2.135.0/linux/validation_layers.html
         */
        'VK_LAYER_KHRONOS_validation' => null,
    ];

    /**
     * @var string[]
     */
    private const DEBUG_EXTENSIONS = [
        /**
         * Due to the nature of the Vulkan interface, there is very little
         * error information available to the developer and application. By
         * enabling optional validation layers and using the
         * VK_EXT_debug_report extension, developers can obtain much more
         * detailed feedback on the application’s use of Vulkan. This extension
         * defines a way for layers and the implementation to call back to the
         * application for events of interest to the application.
         *
         * @see https://www.khronos.org/registry/vulkan/specs/1.2-extensions/man/html/VK_EXT_debug_report.html
         */
        'VK_EXT_debug_report' => 'createDebugCallback',
    ];

    /**
     * @var array|\Closure[]
     */
    private array $callbacks = [];

    /**
     * @return void
     */
    public function register(): void
    {
        $config = $this->app->make(Repository::class);

        if ($config->get('app.debug', false)) {
            $initializer = $this->app->make(InitializerInterface::class);

            $initializer->addLayers(
                $this->filter($this->app->make(Layers::class), self::DEBUG_LAYERS)
            );

            $initializer->addExtensions(
                $this->filter($this->app->make(Extensions::class), self::DEBUG_EXTENSIONS)
            );
        }
    }

    /**
     * @param Collection|NamedTypeInterface[] $collection
     * @param array $defined
     * @return Collection
     */
    private function filter($collection, array $defined): Collection
    {
        return $collection->filter(function (NamedTypeInterface $type) use ($defined) {
            if (\array_key_exists($type->name, $defined)) {
                if ($callback = $defined[$type->name]) {
                    $this->callbacks[] = \Closure::fromCallable([$this, $callback]);
                }

                return true;
            }

            return false;
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        while (\count($this->callbacks)) {
            $this->app->call(\array_shift($this->callbacks));
        }
    }

    /**
     * Extension "VK_EXT_debug_report"
     *
     * @param Vulkan $vk
     * @param LoggerInterface $logger
     * @return void
     */
    private function createDebugCallback(Vulkan $vk, LoggerInterface $logger): void
    {
        $reporter = new VkExtDebugReport($vk, $this->app->make(VkInstance::class));
        $reporter->create($reporter->loggerCallback($logger));
    }
}
