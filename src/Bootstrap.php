<?php

namespace Ihsan\Client\Platform;

use Ihsan\Client\Platform\Http\Kernel;
use Ihsan\Client\Platform\Middleware\ApiClientMiddleware;
use Ihsan\Client\Platform\Middleware\EventDispatcherMiddleware;
use Ihsan\Client\Platform\Middleware\MiddlewareBuilder;
use Ihsan\Client\Platform\Middleware\MiddlewareStack;
use Ihsan\Client\Platform\Middleware\RouterMiddleware;
use Ihsan\Client\Platform\Middleware\TemplatingMiddleware;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
abstract class Bootstrap
{
    /**
     * @return string
     */
    abstract protected function projectDir();

    /**
     * @param Request   $request
     * @param Container $container
     *
     * @return Response
     */
    public function handle(Request $request, Container $container)
    {
        $configurations = $container['config'];
        $configurations = array_merge($configurations, ['project_dir' => $this->projectDir()]);
        $container['config'] = $configurations;

        /** @var EventDispatcher $eventDipatcher */
        $eventDipatcher = $container['internal.event_dispatcher'];
        $kernel = new Kernel($eventDipatcher);

        $middlewareStack = new MiddlewareStack($container);

        /** @var MiddlewareBuilder $middlewareBuilder */
        $middlewareBuilder = $container['internal.middleware_builder'];
        $middlewareBuilder->addMiddleware(RouterMiddleware::class, [], 2047);
        $middlewareBuilder->addMiddleware(EventDispatcherMiddleware::class, 2047);
        $middlewareBuilder->addMiddleware(TemplatingMiddleware::class, [], 2045);
        $middlewareBuilder->addMiddleware(ApiClientMiddleware::class, [], 2043);

        foreach ($middlewareBuilder->getMiddlewares() as $middleware) {
            call_user_func_array([$middlewareStack, 'push'], $middleware);
        }

        $app = $middlewareStack->resolve($kernel);
        $response = $app->handle($request);

        return $response->send();
    }
}
