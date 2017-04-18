<?php

namespace Ihsan\Client\Platform\DependencyInjection;

use Ihsan\Client\Platform\Cache\CacheHandler;
use Ihsan\Client\Platform\Configuration\Configuration;
use Ihsan\Client\Platform\Middleware\MiddlewareBuilder;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class ContainerBuilder
{
    /**
     * @param string $configDir
     * @param array $configs
     *
     * @return Container
     */
    public static function build($configDir, array $configs)
    {
        $container = new Container();

        $configuration = new Configuration($configDir);
        foreach ($configs as $config) {
            $configuration->addResource($config);
        }
        $configuration->process($container);

        $container['internal.cache_handler'] = function ($container) {
            return new CacheHandler();
        };

        $container['internal.configuration'] = function ($container) use ($configuration) {
            return $configuration;
        };

        $container['internal.middleware_builder'] = function ($container) {
            return new MiddlewareBuilder();
        };

        $container['internal.session'] = function ($container) {
            return new Session();
        };

        return $container;
    }
}
