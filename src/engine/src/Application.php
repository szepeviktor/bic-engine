<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic;

use Bic\Application\ExceptionHandler;
use Bic\Exception\InitializationException;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

/**
 * Class Application
 */
class Application extends Container
{
    /**
     * @var string
     */
    private const ERROR_SERVICE_REGISTRATION = 'Can not register a new service "%s" because the app is already booted';

    /**
     * @var string|ServiceProvider[]
     */
    private const CORE_SERVICE_PROVIDERS = [
        Application\Providers\ApplicationServiceProvider::class,
        Application\Providers\ConfigServiceProvider::class,
        Application\Providers\LoggerServiceProvider::class,
        Application\Providers\VulkanServiceProvider::class,
        Application\Providers\VulkanDebugServiceProvider::class,
    ];

    /**
     * @var string
     */
    public const ENGINE_VERSION = '0.0.1';

    /**
     * @var string
     */
    public const ENGINE_NAME = 'BIC Engine';

    /**
     * List of bootable service providers
     *
     * @var array|ServiceProvider[]
     */
    private array $providers = [];

    /**
     * @var string
     */
    public string $path;

    /**
     * @var bool
     */
    private bool $booted = false;

    /**
     * @var \Closure[]
     */
    private array $deferred = [];

    /**
     * Application constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->registerPath($path);

        $this->registerCoreServiceProviders();
    }

    /**
     * @param string $path
     * @return void
     */
    private function registerPath(string $path): void
    {
        $this->path = \realpath($path) ?: $path;

        $this->instance('path', $this->path);
    }

    /**
     * @return void
     */
    private function registerCoreServiceProviders(): void
    {
        foreach (self::CORE_SERVICE_PROVIDERS as $class) {
            $this->register($class);
        }
    }

    /**
     * @param string $provider
     * @return void
     */
    public function register(string $provider): void
    {
        if ($this->booted) {
            throw new InitializationException(\sprintf(self::ERROR_SERVICE_REGISTRATION, $provider));
        }

        /** @var ServiceProvider $instance */
        $this->providers[] = $this->make($provider, ['app' => $this]);
    }

    /**
     * @return void
     */
    private function bootIfNotBooted(): void
    {
        if ($this->booted === false) {
            $this->registerServiceProviders();

            $this->booted = true;

            $this->bootServiceProviders();
        }
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function run(): void
    {
        try {
            $this->bootIfNotBooted();
        } catch (\Throwable $e) {
            $this->onError($e);
        } finally {
            foreach ($this->deferred as $defer) {
                $this->call($defer);
            }

            $this->booted = false;
        }
    }

    /**
     * @param \Closure $callback
     * @return void
     */
    public function defer(\Closure $callback): void
    {
        $this->deferred[] = $callback;
    }

    /**
     * @param \Throwable $e
     * @return void
     */
    private function onError(\Throwable $e): void
    {
        $handler = $this->make(ExceptionHandler::class);
        $handler->throw($e);
    }

    /**
     * @return void
     */
    private function registerServiceProviders(): void
    {
        $providers = [];

        while (\count($this->providers)) {
            $providers[] = $provider = \array_shift($this->providers);
            $provider->register();
        }

        $this->providers = $providers;
    }

    /**
     * @return void
     */
    private function bootServiceProviders(): void
    {
        $providers = [];

        while (\count($this->providers)) {
            $providers[] = $provider = \array_shift($this->providers);

            if (\method_exists($provider, 'boot')) {
                $this->call([$provider, 'boot']);
            }
        }

        $this->providers = $providers;
    }
}
