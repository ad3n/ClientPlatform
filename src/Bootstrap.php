<?php

namespace Ihsan\Client\Platform;

use Ihsan\Client\Platform\Api\Client;
use Ihsan\Client\Platform\Configuration\Configuration;
use Ihsan\Client\Platform\Controller\ControllerResolver;
use Ihsan\Client\Platform\EventListener\RegisterListenerMiddleware;
use Ihsan\Client\Platform\Http\Kernel;
use Ihsan\Client\Platform\Http\RouteMiddleware;
use Ihsan\Client\Platform\Middleware\MiddlewareBuilder;
use Ihsan\Client\Platform\Middleware\MiddlewareStack;
use Ihsan\Client\Platform\Twig\TwigExtensionMiddleware;
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
     * @param array                       $values
     */
    public function __construct(CacheItemPoolInterface $cachePool = null, array $values = array())
    {
        parent::__construct($values);
        $this->cachePool = $cachePool;

        $this['project_dir'] = $this->projectDir();
    }

    /**
     * @param string $configDir
     */
    public function boot($configDir = 'configs')
    {
        if ($this->booted) {
            throw new \RuntimeException(sprintf('Application is booted.'));
        }

        $cachePath = sprintf('%s/caches', $this->projectDir());
        if (!$this->cachePool) {
            $this->cachePool = new FilesystemAdapter('client_platform', 3600, $cachePath);
        }

        $cachePool = $this->cachePool;
        $this['internal.cache_handler'] = function () use ($cachePool) {
            return $cachePool;
        };

        $finder = new Finder();
        $finder->in(sprintf('%s/%s', $this->projectDir(), $configDir));
        $finder->ignoreDotFiles(true);
        $files = $finder->files()->name('*.yml');

        $configuration = new Configuration();
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $configuration->addResource($file->getRealPath());
        }
        $configuration->process($this);

        $this->buildContainer();
        $this['internal.template'] = function ($container) use ($cachePath) {
            $templateClass = $container['template']['engine'];
            $viewPath = sprintf('%s/%s', $container['project_dir'], $container['template']['path']);
            if ($templateClass) {
                $templateEngine = new $templateClass($viewPath, $cachePath);
            } else {
                $templateEngine = new TwigTemplateEngine($viewPath, $cachePath);
            }

            return $templateEngine;
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
        $middlewareBuilder->addMiddleware(RouteMiddleware::class, [], 2047);
        $middlewareBuilder->addMiddleware(RegisterListenerMiddleware::class, [], 2047);
        $middlewareBuilder->addMiddleware(TwigExtensionMiddleware::class, [], 2045);

        foreach ($this['middlewares'] as $middleware) {
            $middlewareBuilder->addMiddleware($middleware['class'], $middleware['parameters'], $middleware['priority']);
        }

        /** @var MiddlewareStack $middlewareStack */
        $middlewareStack = $this['internal.middleware_stack'];
        foreach ($middlewareBuilder->getMiddlewares() as $middleware) {
            call_user_func_array([$middlewareStack, 'push'], $middleware);
        }

        $app = $middlewareStack->resolve($this['internal.kernel']);
        $response = $app->handle($request);

        return $response->send();
    }

    private function buildContainer()
    {
        $this['internal.http_client'] = function ($container) {
            $clientClass = $container['http_client'];
            if ($clientClass) {
                $httpClient = new $clientClass($container['internal.session_storage'], $container['api']['base_url'], $container['api']['api_key'], $container['api']['param_key']);
            } else {
                $httpClient = new Client($container['internal.session_storage'], $container['api']['base_url'], $container['api']['api_key'], $container['api']['param_key']);
            }

            return $httpClient;
        };

        $this['internal.controller_resolver'] = function ($container) {
            return new ControllerResolver($container['internal.url_matcher']);
        };

        $this['internal.event_dispatcher'] = function () {
            return new EventDispatcher();
        };

        $this['internal.kernel'] = function ($container) {
            return new Kernel($container['internal.event_dispatcher']);
        };

        $this['internal.middleware_builder'] = function () {
            return new MiddlewareBuilder();
        };

        $this['internal.middleware_stack'] = function ($container) {
            return new MiddlewareStack($container);
        };

        $this['internal.request_context'] = function () {
            return new RequestContext();
        };

        $this['internal.route_collection'] = function () {
            return new RouteCollection();
        };

        $this['internal.url_matcher'] = function ($container) {
            return new UrlMatcher(
                $container['internal.route_collection'],
                $container['internal.request_context']
            );
        };

        $this['internal.session_storage'] = function () {
            return new Session();
        };
    }
}
