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
 * Class Builder
 */
class Builder
{
    /**
     * @var array|Queue[][]
     */
    private array $queues = [];

    /**
     * @var Application
     */
    private Application $app;

    /**
     * Builder constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param Queue $queue
     * @param int $priority
     * @return $this
     */
    public function with(Queue $queue, int $priority = 0): self
    {
        \assert($priority >= 0 && $priority <= 100);

        $this->queues[$priority][] = $queue;

        return $this;
    }

    /**
     * @return Device
     */
    public function create(): Device
    {
        \ksort($this->queues);

        return new Device($this->app, $this->queues);
    }
}
