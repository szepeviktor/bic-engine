<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Type;

use Illuminate\Support\HigherOrderCollectionProxy;
use Serafim\Vulkan\Vulkan;

/**
 * @mixin Collection
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var Vulkan
     */
    protected Vulkan $vk;

    /**
     * Factory constructor.
     *
     * @param Vulkan $vulkan
     */
    public function __construct(Vulkan $vulkan)
    {
        $this->vk = $vulkan;
    }

    /**
     * @param string $type
     * @param callable $applicant
     * @return Collection
     */
    protected function enumerate(string $type, callable $applicant): Collection
    {
        return Enumerator::collect($this->vk, $type, $applicant);
    }

    /**
     * @return Collection
     */
    abstract public function getIterator(): Collection;

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->getIterator()
            ->count()
        ;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->getIterator()->$name(...$arguments);
    }

    /**
     * @param string $name
     * @return HigherOrderCollectionProxy|mixed
     * @noinspection MagicMethodsValidityInspection
     */
    public function __get(string $name)
    {
        return $this->getIterator()->$name;
    }
}
