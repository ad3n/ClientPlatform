<?php

namespace Ihsan\Client\Platform;

use Ihsan\Client\Platform\Http\Kernel;
use Ihsan\Client\Platform\Middleware\ApiClientMiddleware;
use Ihsan\Client\Platform\Middleware\ConfigurationMiddleware;
use Ihsan\Client\Platform\Middleware\EventDispatcherMiddleware;
use Ihsan\Client\Platform\Middleware\MiddlewareBuilder;
use Ihsan\Client\Platform\Middleware\RouterMiddleware;
use Ihsan\Client\Platform\Middleware\TemplatingMiddleware;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class Bootstrap
{
    /**
     * @var MiddlewareBuilder
     */
    private $middlewareBuilder;

    /**
     * @var \SplPriorityQueue
     */
    private $middlewares;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     * @param array     $middlewares
     */
    public function __construct(Container $container, array $middlewares = [])
    {
        $this->middlewareBuilder = new MiddlewareBuilder($container);
        $this->middlewares = new \SplPriorityQueue();
        $this->container = $container;

        foreach ($middlewares as $middleware) {
            if (array_key_exists('class', $middleware)) {
                throw new \OutOfRangeException('index "class" not found.');
            }

            $parameters = [];
            $priority = 0;
            if (array_key_exists('parameters', $middleware) && is_array($middleware['parameters'])) {
                $parameters = $middleware['parameters'];
            }

            if (array_key_exists('priority', $middleware)) {
                $priority = (int) $middleware['priority'];
            }

            $this->addMiddleware($middleware['class'], $parameters, $priority);
        }

        $this->container['session'] = function (Container $container) {
            return new Session();
        };
    }

    /**
     * @param string $middleware
     * @param array  $parameters
     * @param int    $priority
     */
    private function addMiddleware($middleware, array $parameters = [], $priority = 0)
    {
        $this->middlewares->insert(array_merge([$middleware], $parameters), $priority);
    }

    /**
     * @param Request $request
     * @param array   $configs
     *
     * @return Response
     */
    public function handle(Request $request, array $configs)
    {
        $eventDipatcher = new EventDispatcher();
        $kernel = new Kernel($eventDipatcher, $configs);

        $this->addMiddleware(ConfigurationMiddleware::class, [$configs], 2049);
        $this->addMiddleware(RouterMiddleware::class, [], 2047);
        $this->addMiddleware(EventDispatcherMiddleware::class, [$eventDipatcher], 2047);
        $this->addMiddleware(TemplatingMiddleware::class, [], 2045);
        $this->addMiddleware(ApiClientMiddleware::class, [], 2043);

        foreach ($this->middlewares as $middleware) {
            call_user_func_array([$this->middlewareBuilder, 'push'], $middleware);
        }

        $app = $this->middlewareBuilder->resolve($kernel);
        $response = $app->handle($request);

        return $response->send();
    }
}
