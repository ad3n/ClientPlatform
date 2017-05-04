<?php

namespace Ihsan\Client\Platform\Controller;

use Ihsan\Client\Platform\Api\ApiClientAwareInterface;
use Ihsan\Client\Platform\Api\ApiClientAwareTrait;
use Ihsan\Client\Platform\Template\TemplatingAwareInterface;
use Ihsan\Client\Platform\Template\TemplatingAwareTrait;
use Pimple\Container;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
abstract class AbstractController implements ControllerInterface, TemplatingAwareInterface, ApiClientAwareInterface
{
    use TemplatingAwareTrait;
    use ApiClientAwareTrait;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}
