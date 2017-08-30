<?php

namespace Ihsan\Client\Platform\DependencyInjection;

use Pimple\Container;

/**
 * @author Muhamad Surya Iksanudin <surya.kejawen@gmail.com>
 */
trait ContainerAwareTrait
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
