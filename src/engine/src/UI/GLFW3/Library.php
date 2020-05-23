<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI\GLFW3;

use Serafim\FFILoader\BitDepth;
use Serafim\FFILoader\Library as BaseLibrary;
use Serafim\FFILoader\OperatingSystem;

/**
 * Class Library
 */
class Library extends BaseLibrary
{
    /**
     * @var string
     */
    private const LIB_ROOT = __DIR__ . '/../../..';

    /**
     * @var string
     */
    private const LIB_OUT = self::LIB_ROOT . '/out';

    /**
     * @var string
     */
    public const LIB_HEADERS = self::LIB_ROOT . '/resources/headers/glfw3.h';

    /**
     * @var string
     */
    private const LIB_WIN_X64 = self::LIB_ROOT . '/bin/x64/glfw3.dll';

    /**
     * @var string
     */
    private const LIB_WIN_X86 = self::LIB_ROOT . '/bin/x86/glfw3.dll';

    /**
     * @var string
     */
    private const LIB_LINUX = 'glfw3.so';

    /**
     * @var string
     */
    private const LIB_DARWIN = 'glfw3.dylib';

    /**
     * @var string|null
     */
    private ?string $version = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'glfw3';
    }

    /**
     * @param string $library
     * @return string
     */
    public function getVersion(string $library): string
    {
        if ($this->version === null) {
            [$maj, $min, $patch] = [\FFI::new('int'), \FFI::new('int'), \FFI::new('int')];

            /** @var \FFI|object $ffi */
            $ffi = \FFI::cdef('void glfwGetVersion(int* major, int* minor, int* rev);', $library);
            $ffi->glfwGetVersion(\FFI::addr($maj), \FFI::addr($min), \FFI::addr($patch));

            return \sprintf('%d.%d.%d', $maj->cdata, $min->cdata, $patch->cdata);
        }

        return $this->version;
    }

    /**
     * @return string
     */
    public function getHeaders(): string
    {
        return self::LIB_HEADERS;
    }

    /**
     * @return string
     */
    public function getOutputDirectory(): string
    {
        return self::LIB_OUT;
    }

    /**
     * @param OperatingSystem $os
     * @param BitDepth $bits
     * @return string|null
     */
    public function getLibrary(OperatingSystem $os, BitDepth $bits): ?string
    {
        switch (true) {
            case $os->isWindows():
                return $bits->is64BitDepth() ? self::LIB_WIN_X64 : self::LIB_WIN_X86;

            case $os->isLinux():
                return self::LIB_LINUX;

            case $os->isMac():
                return self::LIB_DARWIN;
        }

        return null;
    }

    /**
     * @param OperatingSystem $os
     * @param BitDepth $bits
     * @return string|null
     */
    public function suggest(OperatingSystem $os, BitDepth $bits): ?string
    {
        switch (true) {
            case $os->isWindows():
                return 'Try to open issue on GitHub: https://github.com/SerafimArts/ffi-sdl/issues';

            case $os->isLinux():
                return 'Dependency installation required: "apt-get install libglfw3-dev -y"';

            case $os->isMac():
                return 'Dependency installation required: "brew install glfw3"';
        }

        return null;
    }
}
