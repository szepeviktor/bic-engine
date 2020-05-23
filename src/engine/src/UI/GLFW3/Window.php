<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI\GLFW3;

use Bic\Application;
use Bic\Type\Collection;
use Bic\UI\Window as BaseWindow;
use FFI\CData;

/**
 * Class Window
 */
class Window extends BaseWindow
{
    /**
     * GLFW3Window constructor.
     *
     * @param \FFI|object $ffi
     * @param CData $window
     */
    public function __construct(\FFI $ffi, CData $window)
    {
        parent::__construct($ffi, $window);

        $this->centrize($ffi, $window);
    }

    /**
     * @param \FFI|object $ffi
     * @param CData $window
     * @return void
     */
    private function centrize(\FFI $ffi, CData $window): void
    {
        $mode = $ffi->glfwGetVideoMode(
            $monitor = $ffi->glfwGetPrimaryMonitor()
        );

        if ($mode === null) {
            return;
        }

        [$x, $y] = $this->getMonitorPosition($ffi, $monitor);
        [$w, $h] = $this->getWindowSize($ffi, $window);

        $ffi->glfwSetWindowPos($window, $x + ($mode->width - $w) / 2, $y + ($mode->height - $h) / 2);
    }

    /**
     * @param \FFI|object $ffi
     * @param CData $monitor
     * @return array|int[]
     */
    private function getMonitorPosition(\FFI $ffi, CData $monitor): array
    {
        [$x, $y] = [$this->ffi->new('int32_t'), $this->ffi->new('int32_t')];

        $ffi->glfwGetMonitorPos($monitor, \FFI::addr($x), \FFI::addr($y));

        return [$x->cdata, $y->cdata];
    }

    /**
     * @return void
     */
    public function show(): void
    {
        $this->ffi->glfwShowWindow($this->window);
    }

    /**
     * @return void
     */
    public function hide(): void
    {
        $this->ffi->glfwHideWindow($this->window);
    }

    /**
     * @param \FFI|object $ffi
     * @param CData $window
     * @return array
     */
    private function getWindowSize(\FFI $ffi, CData $window): array
    {
        [$w, $h] = [$this->ffi->new('int32_t'), $this->ffi->new('int32_t')];

        $ffi->glfwGetWindowSize($window, \FFI::addr($w), \FFI::addr($h));

        return [$w->cdata, $h->cdata];
    }

    /**
     * @return Collection|string[]|mixed
     */
    public function getExtensions(): Collection
    {
        $count = $this->ffi->new('uint32_t');
        $extensions = $this->ffi->glfwGetRequiredInstanceExtensions(\FFI::addr($count));

        if ($extensions === null) {
            return Collection::make();
        }

        return Collection::make(
            $this->toStringArray($count->cdata, $extensions)
        );
    }

    /**
     * @return void
     */
    protected function destroy(): void
    {
        $this->ffi->glfwDestroyWindow($this->window);
    }
}
