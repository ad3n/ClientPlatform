<?php

namespace Ihsan\Client\Platform\Middleware;

use Ihsan\Client\Platform\Controller\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class RouterMiddleware implements HttpKernelInterface
{
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
     * @param int $type
     * @param bool $catch
     *
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $configurations = $request->attributes->get('_config', array());
        $router = new RouteCollection();

        if (array_key_exists('routes', $configurations)) {
            foreach ($configurations['routes'] as $route) {
                $this->buildRoute($router, $route);
            }
        }

        $controllerResolver = new ControllerResolver($router, $request);
        $request->attributes->add($controllerResolver->resolve($request->getPathInfo()));

        return $this->app->handle($request, $type, $catch);
    }

    private function buildRoute(RouteCollection $router, array $config)
    {
        if (!key_exists('controller', $config)) {
            throw new InvalidParameterException(sprintf('"controller" key must be set.'));
        }

        if (!key_exists('path', $config)) {
            throw new InvalidParameterException(sprintf('"path" must be set.'));
        }

        if (!key_exists('methods', $config)) {
            $config['methods'] = array();
        }

        $router->add($config['controller'], new Route($config['path'], array(), array(), array(), '', array(), $config['methods']));
    }
}
