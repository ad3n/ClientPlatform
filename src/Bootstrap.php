<?php

namespace Ihsan\Client\Platform;

use Ihsan\Client\Platform\Api\ApiClientMiddleware;
use Ihsan\Client\Platform\Api\GuzzleClient;
use Ihsan\Client\Platform\Configuration\Configuration;
use Ihsan\Client\Platform\Controller\ControllerResolver;
use Ihsan\Client\Platform\Event\EventDispatcherMiddleware;
use Ihsan\Client\Platform\Http\Kernel;
use Ihsan\Client\Platform\Http\RoutingMiddleware;
use Ihsan\Client\Platform\Middleware\MiddlewareBuilder;
use Ihsan\Client\Platform\Middleware\MiddlewareStack;
use Ihsan\Client\Platform\Template\TemplatingMiddleware;
use Ihsan\Client\Platform\Twig\TwigTemplateEngine;
use Pimple\Container;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
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
     * @var bool
     */
    private $booted = false;

    /**
     * @var CacheItemPoolInterface
     */
    private $cachePool;

    /**
     * @return string
     */
    abstract protected function projectDir();

    /**
     * @param CacheItemPoolInterface|null $cachePool
     * @param array                 $values
     */
    public function __construct(CacheItemPoolInterface $cachePool = null, array $values = array())
    {
        parent::__construct($values);

        if (null === $cachePool) {
            $this->cachePool = new FilesystemAdapter();
        }

        $this['project_dir'] = $this->projectDir();
    }

    /**
     * @param string $configDir
     */
    public function boot($configDir)
    {
        if ($this->booted) {
            throw new \RuntimeException(sprintf('Application is booted.'));
        }

        $cachePool = $this->cachePool;
        $this['internal.cache_handler'] = function ($container) use ($cachePool) {
            return $cachePool;
        };

        $finder = new Finder();
        $finder->in($configDir);
        $finder->ignoreDotFiles(true);
        $files = $finder->files()->name('*.yml');

        $configuration = new Configuration();
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $configuration->addResource($file->getRealPath());
        }
        $configuration->process($this);

        $this['internal.http_client'] = function ($container) {
            return new GuzzleClient($container['internal.session'], $container['base_url']);
        };

        $this['internal.controller_resolver'] = function ($container) {
            return new ControllerResolver($container['internal.url_matcher']);
        };

        $this['internal.event_dispatcher'] = function ($container) {
            return new EventDispatcher();
        };

        $this['internal.kernel'] = function ($container) {
            return new Kernel($container['internal.event_dispatcher']);
        };

        $this['internal.middleware_builder'] = function ($container) {
            return new MiddlewareBuilder();
        };

        $this['internal.middleware_stack'] = function ($container) {
            return new MiddlewareStack($container);
        };

        $this['internal.request_context'] = function ($container) {
            return new RequestContext();
        };

        $this['internal.route_collection'] = function ($container) {
            return new RouteCollection();
        };

        $this['internal.url_matcher'] = function ($container) {
            return new UrlMatcher(
                $container['internal.route_collection'],
                $container['internal.request_context']
            );
        };

        $this['internal.session'] = function ($container) {
            return new Session();
        };

        $this['internal.template'] = function ($container) {
            $viewPath = sprintf('%s%s', $container['project_dir'], $container['template']['path']);
            $cachePath = sprintf('%s%s', $container['project_dir'], $container['template']['cache_dir']);

            return new TwigTemplateEngine($viewPath, $cachePath);
        };

        $this->booted = true;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        /** @var MiddlewareBuilder $middlewareBuilder */
        $middlewareBuilder = $this['internal.middleware_builder'];
        $middlewareBuilder->addMiddleware(RoutingMiddleware::class, [], 2047);
        $middlewareBuilder->addMiddleware(EventDispatcherMiddleware::class, [], 2047);
        $middlewareBuilder->addMiddleware(TemplatingMiddleware::class, [], 2045);
        $middlewareBuilder->addMiddleware(ApiClientMiddleware::class, [], 2043);

        /** @var MiddlewareStack $middlewareStack */
        $middlewareStack = $this['internal.middleware_stack'];
        foreach ($middlewareBuilder->getMiddlewares() as $middleware) {
            call_user_func_array([$middlewareStack, 'push'], $middleware);
        }

        $app = $middlewareStack->resolve($this['internal.kernel']);
        $response = $app->handle($request);

        return $response->send();
    }
}
