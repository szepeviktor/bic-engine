<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI\GLFW3;

/**
 * Interface ConstantsInterface
 */
interface ConstantsInterface
{
    /**
     * @var int
     */
    public const GLFW_CLIENT_API = 0x00022001;

    /**
     * @var int
     */
    public const GLFW_RESIZABLE = 0x00020003;

    /**
     * @var int
     */
    public const GLFW_VISIBLE    = 0x00020004;

    /**
     * @var int
     */
    public const GLFW_NO_API = 0;

    /**
     * @var int
     */
    public const GLFW_FALSE = 0;
}
