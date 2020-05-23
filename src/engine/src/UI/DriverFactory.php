<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI;

use Bic\Application;
use Bic\UI\GLFW3\Driver as GLFW3Provider;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

/**
 * Class DriverFactory
 */
class DriverFactory implements DriverFactoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use LoggerTrait;

    /**
     * @var string[]|UIDriverInterface[]
     */
    private const DEFAULT_PROVIDERS = [
        GLFW3Provider::class,
    ];

    /**
     * @var string
     */
    private const NOTICE_INIT = 'Error while initializing %s window system: %s';

    /**
     * @var int
     */
    private const ERROR_TYPE = '%s must be a subtype of %s interface';

    /**
     * @var array|string[]|UIDriverInterface[]
     */
    private array $providers;

    /**
     * @var Application
     */
    private Application $app;

    /**
     * Factory constructor.
     *
     * @param Application $app
     * @param array|UIDriverInterface[]|string[] $providers
     */
    public function __construct(Application $app, array $providers = self::DEFAULT_PROVIDERS)
    {
        $this->app = $app;
        $this->providers = $providers;
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

    /**
     * @param string|UIDriverInterface $provider
     * @return void
     */
    public function register(string $provider): void
    {
        assert(
            \is_subclass_of($provider, UIDriverInterface::class),
            \sprintf(self::ERROR_TYPE, $provider, UIDriverInterface::class)
        );

        \array_unshift($this->providers, $provider);
        $this->providers = \array_unique($this->providers);
    }

    /**
     * @param string $provider
     * @return void
     */
    public function unregister(string $provider): void
    {
        $filter = fn (string $haystack) => $haystack !== $provider;

        $this->providers = \array_filter($this->providers, $filter);
    }

    /**
     * @return UIDriverInterface
     */
    public function create(): UIDriverInterface
    {
        foreach ($this->providers as $driver) {
            try {
                $this->debug($driver . ' initialization');

                return tap($this->app->make($driver), function() use ($driver) {
                    $this->debug($driver . ' was initialized');
                });
            } catch (\Throwable $e) {
                $this->warning(\sprintf(self::NOTICE_INIT, $driver, $e->getMessage()));
            }
        }

        throw new \LogicException('Can no initialize window system');
    }
}
