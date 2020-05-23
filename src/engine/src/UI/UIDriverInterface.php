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
 * Interface UIProviderInterface
 */
interface UIDriverInterface
{
    /**
     * @param int $width
     * @param int $height
     * @param string $title
     * @return WindowInterface
     */
    public function window(int $width, int $height, string $title): WindowInterface;

    /**
     * @param WindowInterface[] $windows
     * @return void
     */
    public function loop(array $windows): void;
}
