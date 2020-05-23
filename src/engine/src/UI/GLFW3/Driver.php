<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\UI\GLFW3;

use Bic\Type\Version;
use Bic\UI\UIDriver;
use Bic\UI\WindowInterface;
use Psr\Log\LoggerInterface;
use Serafim\FFILoader\LibraryInformation;
use Serafim\FFILoader\Loader;
use Serafim\FFILoader\PreprocessorInterface;
use Serafim\Vulkan\Vulkan;
use Serafim\Vulkan\Support\Library as VkLibrary;

/**
 * Class Driver
 */
class Driver extends UIDriver implements ConstantsInterface
{
    /**
     * @var string
     */
    private const ERROR_UNSUPPORTED_VERSION = 'GLFW >= %s required to support Vulkan API, but %s was found';

    /**
     * @var string
     */
    private const GLFW_MIN_VERSION = '3.2.0';

    /**
     * @var LibraryInformation
     */
    public LibraryInformation $info;

    /**
     * Driver constructor.
     *
     * @param Vulkan $vk
     * @param LoggerInterface|null $logger
     */
    public function __construct(Vulkan $vk, LoggerInterface $logger = null)
    {
        parent::__construct($logger);

        $this->info = $this->compile($vk);

        $this->assertVersion($this->info);
    }

    /**
     * @param LibraryInformation $info
     * @return void
     */
    private function assertVersion(LibraryInformation $info): void
    {
        if (\version_compare($info->version, self::GLFW_MIN_VERSION) < 0) {
            throw new \LogicException(\vsprintf(self::ERROR_UNSUPPORTED_VERSION, [
                self::GLFW_MIN_VERSION,
                $this->info->version
            ]));
        }
    }

    /**
     * @param Vulkan $vulkan
     * @return LibraryInformation
     */
    private function compile(Vulkan $vulkan): LibraryInformation
    {
        $loader = new Loader();

        $this->shareVariables($vulkan, $loader->preprocessor());

        $this->debug('Compilation ' . \realpath(Library::LIB_HEADERS));

        return $loader->load(new Library());
    }

    /**
     * @param Vulkan $vk
     * @param PreprocessorInterface $pre
     * @return void
     */
    private function shareVariables(Vulkan $vk, PreprocessorInterface $pre): void
    {
        $vkLibrary = new VkLibrary(Version::fromString($vk->info->version)->toInt());

        $pre->includeFrom(\dirname($vkLibrary->getHeaders()));
        $pre->includeFrom(\dirname(Library::LIB_HEADERS));

        $pre->define('__vk_version__', $vk->info->version);
        $pre->define('VK_NO_PROTOTYPES', '1');
    }

    /**
     * @param int $width
     * @param int $height
     * @param string $title
     * @return WindowInterface
     */
    public function window(int $width, int $height, string $title): WindowInterface
    {
        $this->logWindowCreating($width, $height, $title);

        $this->info->ffi->glfwInit();
        $this->info->ffi->glfwWindowHint(self::GLFW_CLIENT_API, self::GLFW_NO_API);
        $this->info->ffi->glfwWindowHint(self::GLFW_RESIZABLE, self::GLFW_FALSE);
        $this->info->ffi->glfwWindowHint(self::GLFW_VISIBLE, self::GLFW_FALSE);

        $window = $this->info->ffi->glfwCreateWindow($width, $height, $title, null, null);

        return new Window($this->info->ffi, $window);
    }

    /**
     * @param WindowInterface[]|Window[] $windows
     * @return void
     */
    public function loop(array $windows): void
    {
        (new EventLoop($this->info->ffi))->run($windows);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        try {
            $this->info->ffi->glfwTerminate();
        } catch (\Throwable $e) {
            //
        }
    }
}
