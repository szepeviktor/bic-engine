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
use Bic\Type\Collection;
use FFI\CData;

/**
 * Class Window
 */
abstract class Window implements WindowInterface
{
    /**
     * @var bool
     */
    protected bool $closed = false;

    /**
     * @var CData
     */
    public CData $window;

    /**
     * @var \FFI|object
     */
    protected \FFI $ffi;

    /**
     * Window constructor.
     *
     * @param \FFI $ffi
     * @param CData $window
     */
    public function __construct(\FFI $ffi, CData $window)
    {
        $this->window = $window;
        $this->ffi = $ffi;
    }

    /**
     * @return void
     */
    abstract protected function destroy(): void;

    /**
     * @return void
     */
    public function close(): void
    {
        if ($this->closed === false) {
            $this->closed = true;

            $this->destroy();
        }
    }

    /**
     * @param int $size
     * @param CData $extensions
     * @return array|string[]
     */
    protected function toStringArray(int $size, CData $extensions): array
    {
        $result = [];

        for ($i = 0; $i < $size; ++$i) {
            $result[] = $extensions[$i];
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtensions(): Collection
    {
        return Collection::make();
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }
}
