<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Exception;

use Serafim\Vulkan\Enum\VkResult;

class EngineErrorException extends \RuntimeException
{
    /**
     * @param int $result
     * @return void
     */
    public static function assertVkResult(int $result): void
    {
        if (VkResult::isError($result)) {
            throw new static(VkResult::getDescription($result), $result);
        }
    }
}
