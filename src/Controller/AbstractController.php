<?php

namespace Ihsan\Client\Platform\Controller;

use Ihsan\Client\Platform\Api\ApiClientAwareInterface;
use Ihsan\Client\Platform\Api\ApiClientAwareTrait;
use Ihsan\Client\Platform\Template\TemplatingAwareInterface;
use Ihsan\Client\Platform\Template\TemplatingAwareTrait;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Response;

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
     * @var Response
     */
    protected $response;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
