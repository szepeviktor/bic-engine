<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Class UIDriver
 */
abstract class UIDriver implements UIDriverInterface, LoggerAwareInterface
{
    use LoggerTrait;
    use LoggerAwareTrait;

    /**
     * UIDriver constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        if ($logger) {
            $this->setLogger($logger);
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @param string $title
     * @return void
     */
    protected function logWindowCreating(int $width, int $height, string $title): void
    {
        $this->debug(\sprintf('Initialize "%s" window %d⨯%d', $title, $width, $height));
    }

    /**
     * @param string|int $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}
