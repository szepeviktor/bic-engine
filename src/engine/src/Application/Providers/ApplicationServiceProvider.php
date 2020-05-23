<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Providers;

use Bic\Application;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerInterface;

/**
 * Class ApplicationServiceProvider
 */
class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerApplication();
        $this->registerContainer();
    }

    /**
     * @return void
     */
    private function registerContainer(): void
    {
        $this->app->alias(Application::class, ContainerContract::class);
        $this->app->alias(Application::class, Container::class);
        $this->app->alias(Application::class, ContainerInterface::class);
    }

    /**
     * @return void
     */
    private function registerApplication(): void
    {
        $this->app->instance(Application::class, $this->app);

        if (\get_class($this->app) !== Application::class) {
            $this->app->alias(Application::class, \get_class($this->app));
        }
    }
}
