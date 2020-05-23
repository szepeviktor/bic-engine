<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application;

use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ConfigReader
 */
class ConfigReader
{
    /**
     * @var string
     */
    public const DEFAULT_EXTENSION = '.php';

    /**
     * @var string
     */
    private string $extension;

    /**
     * Reader constructor.
     *
     * @param string $extension
     */
    public function __construct(string $extension = self::DEFAULT_EXTENSION)
    {
        $this->extension = $extension;
    }

    /**
     * @param string|string[] $directories
     * @param string $depth
     * @return array
     */
    public function read($directories, string $depth = '>= 0'): array
    {
        $result = [];

        foreach ($this->directoryIterator($directories, $depth) as $file) {
            Arr::set($result, $this->key($file), $this->include($file));
        }

        return $result;
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    private function key(SplFileInfo $file): string
    {
        $key = $file->getRelativePathname();
        $key = \str_replace(['\\', '/'], '.', $key);

        return \substr($key, 0, -4);
    }

    /**
     * @param SplFileInfo $file
     * @return array
     */
    private function include(SplFileInfo $file): array
    {
        \ob_start();
        /** @noinspection PhpIncludeInspection */
        $current = require $file->getPathname();
        \ob_end_clean();

        return \is_array($current) ? $current : [];
    }

    /**
     * @param string|string[] $directories
     * @param string $depth
     * @return \Traversable|SplFileInfo[]
     */
    private function directoryIterator($directories, string $depth): \Traversable
    {
        return (new Finder())
            ->files()
            ->in($directories)
            ->name('*' . $this->extension)
            ->depth($depth);
    }
}
