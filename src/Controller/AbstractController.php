<?php

namespace Ihsan\Client\Platform\Controller;

use Ihsan\Client\Platform\Api\ApiClientAwareInterface;
use Ihsan\Client\Platform\Api\ApiClientAwareTrait;
use Ihsan\Client\Platform\Template\TemplateAwareInterface;
use Ihsan\Client\Platform\Template\TemplateAwareTrait;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
abstract class AbstractController implements ControllerInterface, TemplateAwareInterface, ApiClientAwareInterface
{
    use TemplateAwareTrait;
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
