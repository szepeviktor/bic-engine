<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Layer;

use Bic\Application\NamedTypeInterface;
use Bic\Type\DataTransferObject;
use Bic\Type\Version;
use FFI\CData;
use Serafim\Vulkan\VkLayerProperties;

/**
 * @property-read string $name
 * @property-read Version $specification
 *
 * @property-read string $description
 * @property-read Version $implementation
 *
 * <code>
 *  typedef struct VkLayerProperties {
 *      char        layerName[VK_MAX_EXTENSION_NAME_SIZE];
 *      uint32_t    specVersion;
 *      uint32_t    implementationVersion;
 *      char        description[VK_MAX_DESCRIPTION_SIZE];
 *  } VkLayerProperties;
 * </code>
 */
class Layer extends DataTransferObject implements NamedTypeInterface
{
    /**
     * Layer constructor.
     *
     * @param CData|VkLayerProperties $layer
     */
    public function __construct(CData $layer)
    {
        parent::__construct([
            'name'           => \FFI::string($layer->layerName),
            'specification'  => Version::fromInt($layer->specVersion),
            'implementation' => Version::fromInt($layer->implementationVersion),
            'description'    => \FFI::string($layer->description),
        ]);
    }
}
