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
    }
}
