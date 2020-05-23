<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Type;

/**
 * Class MemoizedRepository
 */
abstract class MemoizedRepository extends Repository implements MemoizedRepositoryInterface
{
    /**
     * @var Collection|null
     */
    protected ?Collection $memory = null;

    /**
     * @return void
     */
    public function free(): void
    {
        $this->memory = null;
    }

    /**
     * @return Collection
     */
    abstract protected function getRawIterator(): Collection;

    /**
     * @return Collection
     */
    public function getIterator(): Collection
    {
        return $this->memory ??= $this->getRawIterator();
    }
}
