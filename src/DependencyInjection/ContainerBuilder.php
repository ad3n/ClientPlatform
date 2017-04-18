<?php

namespace Ihsan\Client\Platform\DependencyInjection;

use Ihsan\Client\Platform\Api\GuzzleClient;
use Ihsan\Client\Platform\Cache\CacheHandler;
use Ihsan\Client\Platform\Configuration\Configuration;
use Ihsan\Client\Platform\Controller\ControllerResolver;
use Ihsan\Client\Platform\Middleware\MiddlewareBuilder;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class ContainerBuilder
{
    /**
     * @param string $configDir
     * @param array  $configFiles
     *
     * @return Container
     */
    public static function build($configDir, array $configFiles)
    {
        $container = new Container();

        $configuration = new Configuration($configDir);
        foreach ($configFiles as $configFile) {
            $configuration->addResource($configFile);
        }
        $configuration->process($container);

        $container['internal.cache_handler'] = function ($container) {
            return new CacheHandler();
        };

        $container['internal.http_client'] = function ($container) {
            return new GuzzleClient($container['internal.session'], $container['config']['base_url']);
        };

        $container['internal.controller_resolver'] = function ($container) {
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

        return $container;
    }
}
