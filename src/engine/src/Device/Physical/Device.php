<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Device\Physical;

use Bic\Device\Physical\Queue\Repository as QueueRepository;
use Bic\Type\DataTransferObject;
use Bic\Type\Version;
use FFI\CData;
use Serafim\Vulkan\Enum\VkPhysicalDeviceType;
use Serafim\Vulkan\VkPhysicalDevice;
use Serafim\Vulkan\VkPhysicalDeviceProperties;
use Serafim\Vulkan\Vulkan;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read Version $vulkan
 * @property-read Version $driver
 * @property-read int $vendor
 * @property-read int $type
 */
class Device extends DataTransferObject
{
    /**
     * @var CData|VkPhysicalDevice
     */
    public CData $cdata;

    /**
     * @var QueueRepository
     */
    public QueueRepository $queues;

    /**
     * @var Vulkan
     */
    private Vulkan $vk;

    /**
     * Device constructor.
     *
     * @param Vulkan $vk
     * @param CData|VkPhysicalDevice $device
     */
    public function __construct(Vulkan $vk, CData $device)
    {
        $this->vk = $vk;
        $this->cdata = $device;
        $this->queues = new QueueRepository($vk, $this);

        parent::__construct($this->fetchInfo());
    }

    /**
     * @return array
     */
    private function fetchInfo(): array
    {
        /** @var VkPhysicalDeviceProperties $struct */
        $struct = $this->vk->new('VkPhysicalDeviceProperties');
        $this->vk->vkGetPhysicalDeviceProperties($this->cdata, ptr($struct));

        return [
            // uint32_t deviceID;
            'id'     => $struct->deviceID,
            // char deviceName[VK_MAX_PHYSICAL_DEVICE_NAME_SIZE];
            'name'   => \FFI::string($struct->deviceName),
            // uint32_t apiVersion;
            'vulkan' => Version::fromInt($struct->apiVersion),
            // uint32_t driverVersion;
            'driver' => Version::fromInt($struct->driverVersion),
            // uint32_t vendorID;
            'vendor' => $struct->vendorID,
            // VkPhysicalDeviceType deviceType;
            'type'   => $struct->deviceType,
            // uint8_t                             pipelineCacheUUID[VK_UUID_SIZE];
            // VkPhysicalDeviceLimits              limits;
            // VkPhysicalDeviceSparseProperties    sparseProperties;
        ];
    }

    /**
     * @return string
     */
    public function getTypeDescription(): string
    {
        return VkPhysicalDeviceType::getDescription($this->type);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \vsprintf('%s %s (Vulkan %s)', [
            $this->getTypeDescription(),
            $this->name,
            $this->vulkan->toString()
        ]);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        \FFI::free(\FFI::addr($this->cdata));
    }
}
