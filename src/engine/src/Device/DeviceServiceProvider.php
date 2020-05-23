<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Device;

use Bic\Device\Exception\DeviceNotFoundException;
use Bic\Device\Physical\Device as PhysicalDevice;
use Bic\Device\Physical\Repository as PhysicalDevicesRepository;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;
use Serafim\Vulkan\Enum\VkQueueFlagBits;
use Serafim\Vulkan\VkInstance;
use Serafim\Vulkan\Vulkan;

/**
 * Class DeviceServiceProvider
 */
class DeviceServiceProvider extends ServiceProvider implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use LoggerTrait;

    /**
     * @var string
     */
    private const ERROR_DEVICE_NOT_FOUND =
        'Can not find a GPU that meets the requirements (Vulkan %s with graphics and compute abilities)';

    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerPhysicalDevices();
    }

    /**
     * @return void
     */
    private function registerPhysicalDevices(): void
    {
        if (! $this->app->bound(PhysicalDevicesRepository::class)) {
            $this->app->singleton(PhysicalDevicesRepository::class, function () {
                return new PhysicalDevicesRepository(
                    $this->app->make(Vulkan::class),
                    $this->app->make(VkInstance::class)
                );
            });
        }

        $this->app->singleton(PhysicalDevice::class, function () {
            $needle = $this->app->make(Config::class)
                ->get('vulkan.version', '1.0.0')
            ;

            $device = $this->app->make(PhysicalDevicesRepository::class)
                ->each(fn(PhysicalDevice $device) => $this->debug($device . ' was found'))
                ->whereVulkan($needle)
                ->whereQueueType(VkQueueFlagBits::VK_QUEUE_GRAPHICS_BIT)
                ->whereQueueType(VkQueueFlagBits::VK_QUEUE_COMPUTE_BIT)
                ->each(fn(PhysicalDevice $device) => $this->debug($device . ' meets the requirements'))
                ->first()
            ;

            if (! $device) {
                throw new DeviceNotFoundException(\sprintf(self::ERROR_DEVICE_NOT_FOUND, $needle));
            }

            return $device;
        });
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
}
