<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Device\Physical;

use Bic\Type\Enumerator;
use Bic\Type\MemoizedRepository as BaseRepository;
use FFI\CData;
use Serafim\Vulkan\VkInstance;
use Serafim\Vulkan\Vulkan;

/**
 * @mixin Collection
 */
class Repository extends BaseRepository
{
    /**
     * @var CData|VkInstance
     */
    private CData $instance;

    /**
     * Repository constructor.
     *
     * @param Vulkan $vulkan
     * @param CData|VkInstance $instance
     */
    public function __construct(Vulkan $vulkan, CData $instance)
    {
        $this->instance = $instance;

        parent::__construct($vulkan);
    }

    /**
     * @return Collection|Device[]|mixed
     */
    protected function getRawIterator(): Collection
    {
        return (new Enumerator($this->vk))
            ->through(Collection::class)
            ->enumerate('VkPhysicalDevice', function ($count, $data) {
                return $this->vk->vkEnumeratePhysicalDevices($this->instance, $count, $data);
            })
            ->map(fn(CData $device) => new Device($this->vk, $device))
        ;
    }
}
