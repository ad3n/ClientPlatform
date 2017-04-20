<?php

namespace Ihsan\Client\Platform\Api;

use Ihsan\Client\Platform\Middleware\ContainerAwareMiddlewareInterface;
use Ihsan\Client\Platform\Middleware\ContainerAwareMiddlewareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class ApiClientMiddleware implements HttpKernelInterface, ContainerAwareMiddlewareInterface
{
    use ContainerAwareMiddlewareTrait;

    /**
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * @param HttpKernelInterface $app
     */
    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
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
        $clientClass = $this->container['http_client'];
        if ($clientClass) {
            $httpClient = new $clientClass(
                $this->container['internal.session'],
                $this->container['base_url']
            );
        } else {
            $httpClient = $this->container['internal.http_client'];
        }

        $controller = $request->attributes->get('_controller');
        if ($controller instanceof ApiClientAwareInterface) {
            $controller->setClient($httpClient);
        }

        return $this->app->handle($request, $type, $catch);
    }
}
