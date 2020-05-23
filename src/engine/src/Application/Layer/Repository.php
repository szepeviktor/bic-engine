<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Layer;

use Bic\Type\Collection;
use Bic\Type\MemoizedRepository as BaseRepository;
use FFI\CData;

/**
 * Class Repository
 */
class Repository extends BaseRepository
{
    /**
     * @return Collection|Layer[]
     */
    protected function getRawIterator(): Collection
    {
        return $this->memory ??= $this->enumerate('VkLayerProperties', function ($count, $data) {
            return $this->vk->vkEnumerateInstanceLayerProperties($count, $data);
        })
            ->map(fn(CData $ext) => new Layer($ext));
    }
}
