<?php

namespace Ihsan\Client\Platform\Middleware;

use Bisnis\Middleware\ContainerAwareMiddlewareInterface;
use Bisnis\Middleware\ContainerAwareMiddlewareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class ConfigurationMiddleware implements HttpKernelInterface, ContainerAwareMiddlewareInterface
{
    use ContainerAwareMiddlewareTrait;

    /**
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * @var array
     */
    private $configs;

    /**
     * @param HttpKernelInterface $app
     * @param array               $configs
     */
    public function __construct(HttpKernelInterface $app, array $configs)
    {
        $this->app = $app;
        $this->configs = $configs;
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     *
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->container['config'] = $this->configs;

        return $this->app->handle($request, $type, $catch);
    }
}
