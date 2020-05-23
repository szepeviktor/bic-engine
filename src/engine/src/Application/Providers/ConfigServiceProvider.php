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
use Bic\Application\Config;
use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryInterface;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;

/**
 * @property-read Application $app
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    public const KEY_LOCATOR = 'config';

    /**
     * @var string
     */
    public const KEY_PATH = 'path.config';

    /**
     * @var string
     */
    private string $path = 'config';

    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerConfigPath();
        $this->registerDotEnv();
        $this->registerConfigClass();

        $this->loadProviders();
    }

    /**
     * @return void
     */
    private function loadProviders(): void
    {
        $config = $this->app->make(ConfigContract::class);

        foreach ($config->get('app.providers', []) as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * @return void
     */
    private function registerConfigClass(): void
    {
        $this->app->singleton(ConfigContract::class, function () {
            return Config::fromDirectory($this->getConfigPath());
        });

        $this->app->alias(Config::class, static::KEY_LOCATOR);
    }

    /**
     * @return void
     */
    private function registerDotEnv(): void
    {
        /** @var RepositoryInterface $repository */
        $repository = Env::getRepository();

        foreach (Dotenv::createImmutable($this->app->path)->load() as $name => $value) {
            if (! $repository->has($name)) {
                $repository->set($name, $value);
            }
        }
    }

    /**
     * @return string
     */
    private function getConfigPath(): string
    {
        return $this->app->make(static::KEY_PATH);
    }

    /**
     * @return void
     */
    private function registerConfigPath(): void
    {
        if (! $this->app->bound(static::KEY_PATH)) {
            $path =  $this->app->path . '/' . $this->path;

            $this->app->instance(static::KEY_PATH, $path);
        }
    }
}
