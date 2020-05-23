<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Application;

use Bic\Application;
use Bic\Application\Extension\Extension;
use Bic\Application\Extension\Repository as Extensions;
use Bic\Application\Layer\Layer;
use Bic\Application\Layer\Repository as Layers;
use Bic\Device\Physical\Repository as PhysicalDevices;
use Bic\UI\DriverFactory;
use Bic\UI\UIDriverInterface;

/**
 * Class Environment
 */
class Environment
{
    /**
     * @var UIDriverInterface
     */
    public UIDriverInterface $ui;

    /**
     * @var Extensions|Extension[]
     */
    public Extensions $extensions;

    /**
     * @var Layers|Layer[]
     */
    public Layers $layers;

    /**
     * @var PhysicalDevices
     */
    public PhysicalDevices $physicalDevices;

    /**
     * Environment constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->ui = $this->createUiProvider($app);
        $this->layers = $this->createLayers($app);
        $this->extensions = $this->createExtensions($app);
        $this->physicalDevices = $this->createPhysicalDevices($app);
    }

    /**
     * @param Application $app
     * @return UIDriverInterface
     */
    protected function createUiProvider(Application $app): UIDriverInterface
    {
        return DriverFactory::make($app);
    }

    /**
     * @param Application $app
     * @return Layers
     */
    protected function createLayers(Application $app): Layers
    {
        return new Layers($app);
    }

    /**
     * @param Application $app
     * @return Extensions
     */
    protected function createExtensions(Application $app): Extensions
    {
        return new Extensions($app);
    }

    /**
     * @param Application $app
     * @return PhysicalDevices
     */
    protected function createPhysicalDevices(Application $app): PhysicalDevices
    {
        return new PhysicalDevices($app);
    }
}
