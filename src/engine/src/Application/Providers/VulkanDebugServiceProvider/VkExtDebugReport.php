<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application\Providers\VulkanDebugServiceProvider;

use Bic\Exception\InitializationException;
use FFI\CData;
use Psr\Log\LoggerInterface;
use Serafim\Vulkan\Enum\VkDebugReportFlagBitsEXT;
use Serafim\Vulkan\Enum\VkDebugReportObjectTypeEXT;
use Serafim\Vulkan\Enum\VkStructureType;
use Serafim\Vulkan\VkDebugReportCallbackCreateInfoEXT;
use Serafim\Vulkan\VkDebugReportCallbackEXT;
use Serafim\Vulkan\Vulkan;

/**
 * Class VkExtDebugReport
 */
class VkExtDebugReport
{
    /**
     * @var int
     */
    private const VK_DEBUG_REPORT_BITS =
        VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_INFORMATION_BIT_EXT |
        VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_WARNING_BIT_EXT |
        VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_PERFORMANCE_WARNING_BIT_EXT |
        VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_ERROR_BIT_EXT |
        VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_DEBUG_BIT_EXT;

    /**
     * @var Vulkan
     */
    private Vulkan $vk;

    /**
     * @var CData
     */
    private CData $instance;

    /**
     * VkExtDebugReport constructor.
     *
     * @param Vulkan $vk
     * @param CData $instance
     */
    public function __construct(Vulkan $vk, CData $instance)
    {
        $this->vk = $vk;
        $this->instance = $instance;
    }

    /**
     * @param LoggerInterface $logger
     * @return \Closure
     */
    public function loggerCallback(LoggerInterface $logger): \Closure
    {
        /**
         * @var int $type VkFlags
         * @var VkDebugReportObjectTypeEXT|int $obj VkDebugReportObjectTypeEXT
         * @var int $src uint64_t
         * @var int $loc size_t
         * @var int $code int32_t
         * @var string $layer const char*
         * @var string $message const char*
         * @var mixed $data void*
         */
        return static function (int $type, $obj, $src, $loc, $code, $layer, $message, $data) use ($logger): void {
            $payload = ['code'  => $code, 'layer' => $layer];

            $report = \vsprintf('0x%s (%s): %s', [
                \dechex($src),
                VkDebugReportObjectTypeEXT::getDescription($obj),
                $message,
            ]);

            switch ($type) {
                case VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_INFORMATION_BIT_EXT:
                    $logger->info($report, $payload);
                    break;

                case VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_WARNING_BIT_EXT:
                    $logger->warning($report, $payload);
                    break;

                case VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_PERFORMANCE_WARNING_BIT_EXT:
                case VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_DEBUG_BIT_EXT:
                    $logger->debug($report, $payload);
                    break;

                case VkDebugReportFlagBitsEXT::VK_DEBUG_REPORT_ERROR_BIT_EXT:
                    $logger->error($report, $payload);
                    break;
            }
        };
    }

    /**
     * @param \Closure $callback
     * @return void
     */
    public function create(\Closure $callback): void
    {
        $info = $this->vk->new('VkDebugReportCallbackCreateInfoEXT');
        $info->sType = VkStructureType::VK_STRUCTURE_TYPE_DEBUG_REPORT_CALLBACK_CREATE_INFO_EXT;
        $info->pfnCallback = $callback;
        $info->flags = self::VK_DEBUG_REPORT_BITS;

        $proc = $this->createVkCreateDebugReportCallbackEXT($this->vk, $this->instance);

        $status = $proc($this->instance, \FFI::addr($info), null, \FFI::addr(
            $this->vk->new('VkDebugReportCallbackEXT')
        ));

        InitializationException::assertVkResult($status);
    }

    /**
     * <code>
     * VkResult vkCreateDebugReportCallbackEXT(
     *      VkInstance                                  instance,
     *      const VkDebugReportCallbackCreateInfoEXT*   pCreateInfo,
     *      const VkAllocationCallbacks*                pAllocator,
     *      VkDebugReportCallbackEXT*                   pCallback
     * );
     * </code>
     *
     * @param Vulkan $vk
     * @param CData $instance
     * @return callable|CData
     */
    private function createVkCreateDebugReportCallbackEXT(Vulkan $vk, CData $instance)
    {
        $proc = $vk->vkGetInstanceProcAddr($instance, 'vkCreateDebugReportCallbackEXT');

        return $vk->cast('PFN_vkCreateDebugReportCallbackEXT', $proc);
    }
}
