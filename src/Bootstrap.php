<?php

namespace Ihsan\Client\Platform;

use Ihsan\Client\Platform\Api\GuzzleClient;
use Ihsan\Client\Platform\Cache\CacheHandler;
use Ihsan\Client\Platform\Configuration\Configuration;
use Ihsan\Client\Platform\Controller\ControllerResolver;
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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
abstract class Bootstrap extends Container
{
    /**
     * @return string
     */
    abstract protected function projectDir();

    /**
     * @return string
     */
    abstract protected function cacheDir();

    /**
     * @param string $configDir
     * @param array $configFiles
     */
    public function boot($configDir, array $configFiles)
    {
        $cacheDir = $this->cacheDir();
        $this['internal.cache_handler'] = function ($container) use ($cacheDir) {
            return new CacheHandler($cacheDir);
        };

        // Process Configuration
        $configuration = new Configuration($configDir);
        foreach ($configFiles as $configFile) {
            $configuration->addResource($configFile);
        }
        $configuration->process($this);

        $this['internal.http_client'] = function ($container) {
            return new GuzzleClient($container['internal.session'], $container['config']['base_url']);
        };

        $this['internal.controller_resolver'] = function ($container) {
            return new ControllerResolver($container['internal.url_matcher']);
        };

        $container['internal.event_dispatcher'] = function ($container) {
            return new EventDispatcher();
        };

        $container['internal.middleware_builder'] = function ($container) {
            return new MiddlewareBuilder();
        };

        $container['internal.request_context'] = function ($container) {
            return new RequestContext();
        };

        $container['internal.route_collection'] = function ($container) {
            return new RouteCollection();
        };

        $container['internal.url_matcher'] = function ($container) {
            return new UrlMatcher(
                $container['internal.route_collection'],
                $container['internal.request_context']
            );
        };

        $container['internal.session'] = function ($container) {
            return new Session();
        };
    }

    /**
     * @param Request   $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $configurations = $this['config'];
        $configurations = array_merge($configurations, ['project_dir' => $this->projectDir()]);
        $this['config'] = $configurations;

        /** @var EventDispatcher $eventDipatcher */
        $eventDipatcher = $this['internal.event_dispatcher'];
        $kernel = new Kernel($eventDipatcher);

        $middlewareStack = new MiddlewareStack($this);

        /** @var MiddlewareBuilder $middlewareBuilder */
        $middlewareBuilder = $this['internal.middleware_builder'];
        $middlewareBuilder->addMiddleware(RouterMiddleware::class, [], 2047);
        $middlewareBuilder->addMiddleware(EventDispatcherMiddleware::class, [], 2047);
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
