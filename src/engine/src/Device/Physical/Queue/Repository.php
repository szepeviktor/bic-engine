<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Device\Physical\Queue;

use Bic\Application;
use Bic\Device\Physical\Device;
use Bic\Type\Collection;
use Bic\Type\Enumerator;
use Bic\Type\MemoizedRepository as BaseRepository;
use FFI\CData;
use Serafim\Vulkan\Vulkan;

/**
 * Class Repository
 */
class Repository extends BaseRepository
{
    /**
     * @var Device
     */
    private Device $device;

    /**
     * Repository constructor.
     *
     * @param Vulkan $vk
     * @param Device $device
     */
    public function __construct(Vulkan $vk, Device $device)
    {
        $this->device = $device;

        parent::__construct($vk);
    }

    /**
     * @return Collection|Queue[]
     */
    protected function getRawIterator(): Collection
    {
        return $this->enumerate('VkQueueFamilyProperties', function ($count, $data): void {
            $this->vk->vkGetPhysicalDeviceQueueFamilyProperties($this->device->cdata, $count, $data);
        })
            ->map(fn(CData $data, int $i): Queue => new Queue($i, $data));
    }
}
