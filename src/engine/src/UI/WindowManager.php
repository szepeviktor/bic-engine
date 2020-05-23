<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI;

use Bic\UI\Exception\UnknownWindowException;

/**
 * Class WindowManager
 */
class WindowManager implements WindowManagerInterface
{
    /**
     * @var string
     */
    private string $default;

    /**
     * @var array|WindowInterface[]
     */
    private array $windows = [];

    /**
     * @var array|\Closure[]
     */
    private array $initializers = [];

    /**
     * WindowManager constructor.
     *
     * @param string $default
     */
    public function __construct(string $default)
    {
        $this->default = $default;
    }

    /**
     * @param string $name
     * @param \Closure $initializer
     * @return void
     */
    public function register(string $name, \Closure $initializer): void
    {
        $this->initializers[$name] = $initializer;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name = null): WindowInterface
    {
        $name ??= $this->default;

        if (! isset($this->initializers[$name])) {
            throw new UnknownWindowException('Window "' . $name . '" not found');
        }

        if (! isset($this->windows[$name])) {
            $this->windows[$name] = $this->initializers[$name]();
        }

        return $this->windows[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function all(): iterable
    {
        foreach (\array_keys($this->initializers) as $name) {
            yield $this->get($name);
        }
    }
}
