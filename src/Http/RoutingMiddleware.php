<?php

namespace Ihsan\Client\Platform\Http;

use Ihsan\Client\Platform\Controller\ControllerResolver;
use Ihsan\Client\Platform\Middleware\ContainerAwareMiddlewareInterface;
use Ihsan\Client\Platform\Middleware\ContainerAwareMiddlewareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class RoutingMiddleware implements HttpKernelInterface, ContainerAwareMiddlewareInterface
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
        $router = $this->container['internal.route_collection'];
        foreach ($this->container['routes'] as $route) {
            $this->buildRoute($router, $route);
        }

        /** @var RequestContext $requestContext */
        $requestContext = $this->container['internal.request_context'];
        $requestContext->fromRequest($request);

        /** @var ControllerResolver $controllerResolver */
        $controllerResolver = $this->container['internal.controller_resolver'];
        $request->attributes->add($controllerResolver->resolve($request));

        $controller = $request->attributes->get('_controller');
        $request->attributes->replace(array('_controller' => new $controller($this->container)));

        return $this->app->handle($request, $type, $catch);
    }

    /**
     * @param RouteCollection $router
     * @param array           $config
     */
    private function buildRoute(RouteCollection $router, array $config)
    {
        if (!key_exists('controller', $config)) {
            throw new InvalidParameterException(sprintf('"controller" key must be set.'));
        }

        if (!key_exists('path', $config)) {
            throw new InvalidParameterException(sprintf('"path" must be set.'));
        }

        $methods = [];
        if (key_exists('methods', $config) && !empty($config['methods'])) {
            $methods = $config['methods'];
        }

        $router->add($config['controller'], new Route($config['path'], [], [], [], '', [], $methods));
    }
}
