<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Device\Physical\Queue;

use Bic\Type\DataTransferObject;
use FFI\CData;
use Serafim\Vulkan\VkQueueFamilyProperties;

/**
 * @property-read int $id
 * @property-read int $flags
 * @property-read int $count
 */
class Queue extends DataTransferObject
{
    /**
     * Queue constructor.
     *
     * @param int $id
     * @param CData|VkQueueFamilyProperties $queue
     */
    public function __construct(int $id, CData $queue)
    {
        parent::__construct([
            'id'    => $id,
            'flags' => $queue->queueFlags,
            'count' => $queue->queueCount,
        ]);
    }

    /**
     * @param int ...$flags
     * @return bool
     */
    public function hasFlags(int ...$flags): bool
    {
        foreach ($flags as $flag) {
            if (! $this->hasFlag($flag)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $flags
     * @return bool
     */
    public function hasFlag(int $flags): bool
    {
        return (bool)($this->flags & $flags);
    }
}
