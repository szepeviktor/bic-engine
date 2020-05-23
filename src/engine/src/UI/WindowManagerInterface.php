<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI;

/**
 * Interface WindowManagerInterface
 */
interface WindowManagerInterface
{
    /**
     * @param string|null $name
     * @return WindowInterface
     */
    public function get(string $name = null): WindowInterface;

    /**
     * @return iterable|WindowInterface[]
     */
    public function all(): iterable;
}
