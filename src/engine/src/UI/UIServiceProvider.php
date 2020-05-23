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
use Bic\Application\InitializerInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;

/**
 * @property-read Application $app
 */
class UIServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerDriverFactory();
        $this->registerDriver();

        $this->registerWindowManager();
        $this->registerDefaultWindow();

        $this->loadWindowExtensions();
    }

    /**
     * @return void
     */
    private function registerDriverFactory(): void
    {
        if (! $this->app->bound(DriverFactoryInterface::class)) {
            $this->app->singleton(DriverFactoryInterface::class, function () {
                return new DriverFactory($this->app);
            });
        }
    }

    /**
     * @return void
     */
    private function registerDriver(): void
    {
        if (! $this->app->bound(UIDriverInterface::class)) {
            $this->app->singleton(UIDriverInterface::class, function (): UIDriverInterface {
                $config = $this->app->make(Config::class);

                return $this->loadDriver(
                    $config->get('ui.driver')
                );
            });
        }
    }

    /**
     * @param string|UIDriverInterface|null $driver
     * @return UIDriverInterface
     */
    private function loadDriver($driver): UIDriverInterface
    {
        switch (true) {
            case \is_string($driver):
                return $this->app->make($driver);

            case \is_object($driver):
                return $driver;

            default:
                $factory = $this->app->make(DriverFactoryInterface::class);

                return $factory->create();
        }
    }

    /**
     * @return void
     */
    private function registerWindowManager(): void
    {
        if (! $this->app->bound(WindowManagerInterface::class)) {
            $this->app->singleton(WindowManagerInterface::class, function () {
                $config = $this->app->make(Config::class);
                $driver = $this->app->make(UIDriverInterface::class);

                $manager = new WindowManager($config->get('ui.window'));

                foreach ($config->get('ui.windows', []) as $name => $window) {
                    $manager->register($name, static function () use ($window, $driver, $config) {
                        return $driver->window(
                            $window['width'] ?? 640,
                            $window['height'] ?? 480,
                            $window['name'] ?? $config->get('app.name', 'Window')
                        );
                    });
                }

                return $manager;
            });
        }
    }

    /**
     * @return void
     */
    private function registerDefaultWindow(): void
    {
        if (! $this->app->bound(WindowInterface::class)) {
            $this->app->singleton(WindowInterface::class, function () {
                $manager = $this->app->make(WindowManagerInterface::class);

                return $manager->get();
            });
        }
    }

    /**
     * @param Config $config
     * @return void
     */
    public function boot(Config $config): void
    {
        if (\count($config->get('ui.windows', []))) {
            $driver = $this->app->make(UIDriverInterface::class);
            $manager = $this->app->make(WindowManagerInterface::class);

            $this->app->defer(fn() => $driver->loop([...$manager->all()]));
        }
    }

    /**
     * @return void
     */
    private function loadWindowExtensions(): void
    {
        $window = $this->app->make(WindowInterface::class);
        $initializer = $this->app->make(InitializerInterface::class);

        $initializer->addExtensions($window->getExtensions());
    }
}
