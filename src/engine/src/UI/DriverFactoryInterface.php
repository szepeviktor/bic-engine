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
 * Interface DriverFactoryInterface
 */
interface DriverFactoryInterface
{
    /**
     * @param string $provider
     * @return void
     */
    public function register(string $provider): void;

    /**
     * @param string $provider
     * @return void
     */
    public function unregister(string $provider): void;

    /**
     * @return UIDriverInterface
     */
    public function create(): UIDriverInterface;
}
