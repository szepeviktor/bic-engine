<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application;

use Bic\Application\Extension\Extension;
use Bic\Application\Layer\Layer;

/**
 * Interface InitializerInterface
 */
interface InitializerInterface
{
    /**
     * @param iterable|string[]|Extension[] $extensions
     * @return void
     */
    public function addOptionalExtensions(iterable $extensions): void;

    /**
     * @param iterable|string[]|Extension[] $extensions
     * @return void
     */
    public function addExtensions(iterable $extensions): void;

    /**
     * @param iterable|string[]|Layer[] $layers
     * @return void
     */
    public function addOptionalLayers(iterable $layers): void;

    /**
     * @param iterable|string[]|Layer[] $layers
     * @return void
     */
    public function addLayers(iterable $layers): void;
}
