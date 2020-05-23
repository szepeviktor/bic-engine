<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Extension;

use Bic\Application\NamedTypeInterface;
use Bic\Type\DataTransferObject;
use Bic\Type\Version;
use FFI\CData;
use Serafim\Vulkan\VkExtensionProperties;

/**
 * @property-read string $name
 * @property-read Version $specification
 *
 * <code>
 *  typedef struct VkExtensionProperties {
 *      char        extensionName[VK_MAX_EXTENSION_NAME_SIZE];
 *      uint32_t    specVersion;
 *  } VkExtensionProperties;
 * </code>
 */
class Extension extends DataTransferObject implements NamedTypeInterface
{
    /**
     * Extension constructor.
     *
     * @param CData|VkExtensionProperties $extension
     */
    public function __construct(CData $extension)
    {
        parent::__construct([
            'name'          => \FFI::string($extension->extensionName),
            'specification' => Version::fromInt($extension->specVersion),
        ]);
    }
}
