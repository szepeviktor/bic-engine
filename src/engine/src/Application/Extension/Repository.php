<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Extension;

use Bic\Type\Collection;
use Bic\Type\MemoizedRepository as BaseRepository;
use FFI\CData;

/**
 * Class Repository
 */
class Repository extends BaseRepository
{
    /**
     * @return Collection|Extension[]
     */
    protected function getRawIterator(): Collection
    {
        return $this->enumerate('VkExtensionProperties', function ($count, $data) {
            return $this->vk->vkEnumerateInstanceExtensionProperties(null, $count, $data);
        })
            ->map(fn(CData $ext) => new Extension($ext));
    }
}
