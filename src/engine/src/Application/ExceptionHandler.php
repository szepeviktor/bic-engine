<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

/**
 * Class ExceptionHandler
 */
class ExceptionHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use LoggerTrait;

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

    /**
     * @param \Throwable $e
     * @return void
     */
    public function throw(\Throwable $e): void
    {
        $this->emergency($this->formatLog($e));

        \file_put_contents('php://stderr', $this->formatStdout($e));

        exit($e->getCode() ?: -1);
    }

    /**
     * @param \Throwable $e
     * @return string
     */
    protected function formatStdout(\Throwable $e): string
    {
        $delimiter = \str_repeat('=', 120);

        $trace = \explode("\n", $e->getTraceAsString());
        $trace = \array_map(fn (string $line) => '  ' . $line, $trace);

        return \vsprintf("%s\n  %s\n  in %s:%d\n%s\n%s\n%s\n", [
            $delimiter,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $delimiter,
            \implode("\n", $trace),
            $delimiter
        ]);
    }

    /**
     * @param \Throwable $e
     * @return string
     */
    protected function formatLog(\Throwable $e): string
    {
        return \vsprintf("%s in %s:%d", [
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ]);
    }
}
