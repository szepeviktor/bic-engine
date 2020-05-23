<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI\GLFW3;

use Bic\UI\WindowInterface;

/**
 * Class EventLoop
 */
class EventLoop
{
    /**
     * @var \FFI|object
     */
    private \FFI $ffi;

    /**
     * GLFW3Loop constructor.
     *
     * @param \FFI $ffi
     */
    public function __construct(\FFI $ffi)
    {
        $this->ffi = $ffi;
    }

    /**
     * @param WindowInterface[] $windows
     * @return void
     */
    public function run(array $windows): void
    {
        foreach ($windows as $window) {
            $window->show();
        }

        while (\count($windows)) {
            $this->ffi->glfwPollEvents();

            foreach ($windows as $index => $window) {
                if ($this->ffi->glfwWindowShouldClose($window->window)) {
                    $window->close();
                    unset($windows[$index]);
                }
            }
        }
    }
}
