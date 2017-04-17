<?php

namespace Bisnis\Middleware;

use Pimple\Container;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
trait ContainerAwareMiddlewareTrait
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
