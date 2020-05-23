<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application;

use Illuminate\Config\Repository;

/**
 * Class Configuration
 */
class Config extends Repository
{
    /**
     * @var ConfigReader|null
     */
    private static ?ConfigReader $reader = null;

    /**
     * @param string|string[] $directories
     * @param string $depth
     * @return static
     */
    public static function fromDirectory($directories, string $depth = '>= 0'): self
    {
        return new static(self::readDirectory($directories, $depth));
    }

    /**
     * @return ConfigReader
     */
    protected static function getReader(): ConfigReader
    {
        return self::$reader ??= new ConfigReader();
    }

    /**
     * @param string|string[] $directories
     * @param string $depth
     * @return array
     */
    private static function readDirectory($directories, string $depth = '>= 0'): array
    {
        return static::getReader()->read($directories, $depth);
    }

    /**
     * @param string|string[] $directories
     * @param string $depth
     * @return $this
     */
    public function withDirectory($directories, string $depth = '>= 0'): self
    {
        $payload = self::readDirectory($directories, $depth);

        $this->items = \array_merge_recursive($this->items, $payload);

        return $this;
    }
}
