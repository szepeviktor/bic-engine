<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Device\Physical;

use Bic\Device\Physical\Device as PhysicalDevice;
use Bic\Device\Physical\Queue\Queue;
use Bic\Type\Collection as BaseCollection;
use Psr\Log\LoggerInterface;
use Serafim\Vulkan\Enum\VkQueueFlagBits;

/**
 * @method Device[] getIterator()
 * @method Device[] all()
 */
class Collection extends BaseCollection
{
    /**
     * @param string $vk
     * @return Collection|Device[]
     */
    public function whereVulkan(string $vk): self
    {
        return $this->filter(fn(PhysicalDevice $device): bool =>
            \version_compare($device->vulkan->toString(), $vk) >= 0
        );
    }

    /**
     * @param int $type
     * @psalm-param VkQueueFlagBits::VK_QUEUE_*_BIT $type
     * @return Collection|Device[]
     */
    public function whereQueueType(int $type): self
    {
        return $this->filter(fn(PhysicalDevice $device) =>
            $device->queues->filter(fn (Queue $queue) => ($queue->flags & $type) === $type)->count()
        );
    }
}
