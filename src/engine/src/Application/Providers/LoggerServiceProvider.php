<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Providers;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerServiceProvider
 */
class LoggerServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(LoggerInterface::class, function () {
            $config = $this->app->make(Config::class);

            $handlers = $this->bootHandlers($config);

            return new Logger(
                $config->get('app.name'),
                \iterator_to_array($handlers)
            );
        });

        $this->app->resolving(LoggerAwareInterface::class, function (LoggerAwareInterface $ctx) {
            $ctx->setLogger($this->app->make(LoggerInterface::class));
        });
    }

    /**
     * @param Config $config
     * @return \Generator|HandlerInterface[]
     */
    private function bootHandlers(Config $config): \Traversable
    {
        foreach ($config->get('app.logger', []) as $handler => $args) {
            yield new $handler(...$args);
        }
    }
}
