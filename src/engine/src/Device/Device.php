<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Device;

use Bic\Application;
use Bic\Device\Physical\Queue\Queue;

/**
 * Class InitializedDevice
 */
class Device
{
    /**
     * @var Application
     */
    private Application $app;

    /**
     * Device constructor.
     *
     * @param Application $app
     * @param array|Queue[][] $queues
     */
    public function __construct(Application $app, array $queues = [])
    {
        $this->app = $app;

        //
        // Device
        //   |
        // Queue#1, Queue#2, Queue#3
        //  0.1       0.2     0.0
        //
        // queueCount = 2
        //
    }
}
