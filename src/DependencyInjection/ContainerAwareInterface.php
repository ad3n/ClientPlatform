<?php

namespace Ihsan\Client\Platform\DependencyInjection;

use Pimple\Container;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
interface ContainerAwareInterface
{
    /**
     * @param Container $container
     */
    public function setContainer(Container $container);
}
