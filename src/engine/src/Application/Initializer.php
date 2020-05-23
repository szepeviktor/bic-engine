<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application;

use Bic\Application\Extension\Extension;
use Bic\Application\Extension\Repository as Extensions;
use Bic\Application\Layer\Layer;
use Bic\Application\Layer\Repository as Layers;
use Bic\Exception\InitializationException;
use Bic\Type\Version;
use FFI\CData;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Serafim\Vulkan\Enum\VkStructureType;
use Serafim\Vulkan\VkApplicationInfo;
use Serafim\Vulkan\VkInstanceCreateInfo;
use Serafim\Vulkan\Vulkan;

/**
 * Class Initializer
 */
class Initializer implements InitializerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use LoggerTrait;

    /**
     * @var array|string[]
     */
    private array $extensions = [];

    /**
     * @var array|string[]
     */
    private array $layers = [];

    /**
     * @var array
     */
    private array $props = [];

    /**
     * @var Vulkan
     */
    private Vulkan $vk;

    /**
     * @var Extensions
     */
    private Extensions $extensionsRepository;

    /**
     * @var Layers
     */
    private Layers $layersRepository;

    /**
     * Initializer constructor.
     *
     * @param Vulkan $vulkan
     * @param Extensions $extensions
     * @param Layers $layers
     */
    public function __construct(Vulkan $vulkan, Extensions $extensions, Layers $layers)
    {
        $this->vk = $vulkan;
        $this->extensionsRepository = $extensions;
        $this->layersRepository = $layers;
    }

    /**
     * @param string|int $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * @param iterable $props
     * @return $this
     */
    public function with(iterable $props): self
    {
        foreach ($props as $name => $value) {
            $this->props[$name] = $value;
        }

        return $this;
    }

    /**
     * @param iterable|string[]|Layer[] $items
     * @return iterable
     */
    private function typeToString(iterable $items): iterable
    {
        foreach ($items as $item) {
            yield $item instanceof NamedTypeInterface ? $item->name : $item;
        }
    }

    /**
     * @param iterable|string[]|Extension[] $extensions
     * @return void
     */
    public function addOptionalExtensions(iterable $extensions): void
    {
        $this->addExtensions(
            $this->extensionsRepository
                ->whereIn('name', $this->typeToString($extensions))
                ->map->name
        );
    }

    /**
     * @param iterable|string[]|Extension[] $extensions
     * @return void
     */
    public function addExtensions(iterable $extensions): void
    {
        foreach ($this->typeToString($extensions) as $extension) {
            $this->extensions[$extension] = $extension;
        }
    }

    /**
     * @param iterable|string[]|Layer[] $layers
     * @return void
     */
    public function addOptionalLayers(iterable $layers): void
    {
        $this->addLayers(
            $this->layersRepository
                ->whereIn('name', $this->typeToString($layers))
                ->map->name
        );
    }

    /**
     * @param iterable|string[]|Layer[] $layers
     * @return void
     */
    public function addLayers(iterable $layers): void
    {
        foreach ($this->typeToString($layers) as $layer) {
            $this->layers[$layer] = $layer;
        }
    }

    /**
     * @return CData
     */
    public function create(): CData
    {
        return $this->init();
    }

    /**
     * @return CData
     */
    private function init(): CData
    {
        $instance = $this->vk->new('VkInstance');

        $appInfo = $this->createApplicationInfo();
        $instanceInfo = $this->createInstanceInfo($appInfo);

        $result = $this->vk->vkCreateInstance(\FFI::addr($instanceInfo), null, \FFI::addr($instance));

        InitializationException::assertVkResult($result);

        return $instance;
    }

    /**
     * @return CData|VkApplicationInfo
     */
    private function createApplicationInfo(): CData
    {
        /** @var VkApplicationInfo $struct */
        $struct = $this->vk->new('VkApplicationInfo', true);
        $struct->sType = VkStructureType::VK_STRUCTURE_TYPE_APPLICATION_INFO;

        $this->configureAppInfo($struct);

        return $struct;
    }

    /**
     * @param CData|VkApplicationInfo $struct
     * @return void
     */
    private function configureAppInfo(CData $struct): void
    {
        foreach ($this->props as $prop => $value) {
            switch (true) {
                case \is_string($value):
                    $value = string($value);
                    break;

                case $value instanceof Version:
                    $value = $value->toInt();
                    break;
            }

            $struct->$prop = $value;
        }
    }

    /**
     * @param CData|VkApplicationInfo $appInfo
     * @return CData|VkInstanceCreateInfo
     */
    private function createInstanceInfo(CData $appInfo): CData
    {
        /** @var VkInstanceCreateInfo $struct */
        $struct = $this->vk->new('VkInstanceCreateInfo');
        $struct->sType = VkStructureType::VK_STRUCTURE_TYPE_INSTANCE_CREATE_INFO;
        $struct->pApplicationInfo = \FFI::addr($appInfo);

        // Fill extensions
        if (\count($this->extensions)) {
            $this->debug('VkInstance extensions: ' . \implode(', ', $this->extensions));

            $struct->enabledExtensionCount = \count($this->extensions);
            $struct->ppEnabledExtensionNames = string($this->extensions);
        }

        // Fill layers
        if (\count($this->layers)) {
            $this->debug('VkInstance layers: ' . \implode(', ', $this->layers));

            $struct->enabledLayerCount = \count($this->layers);
            $struct->ppEnabledLayerNames = string($this->layers);
        }

        return $struct;
    }
}
