<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI;

use Bic\Application\Extension\Extension;
use Bic\Type\Collection;

/**
 * @property-read $window
 */
interface WindowInterface
{
    /**
     * @return void
     */
    public function close(): void;

    /**
     * @return void
     */
    public function show(): void;

    /**
     * @return void
     */
    public function hide(): void;

    /**
     * @return Collection|Extension[]|mixed
     */
    public function getExtensions(): Collection;
}
