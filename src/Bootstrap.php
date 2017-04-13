<?php

namespace Bisnis;

use Ihsan\Client\Platform\Event\FilterController;
use Ihsan\Client\Platform\Http\Kernel;
use Ihsan\Client\Platform\Http\KernelEvents;
use Ihsan\Client\Platform\Middleware\ApiClientMiddleware;
use Ihsan\Client\Platform\Middleware\ConfigurationMiddleware;
use Ihsan\Client\Platform\Middleware\EventDispatcherMiddleware;
use Ihsan\Client\Platform\Middleware\MiddlewareFactory;
use Ihsan\Client\Platform\Middleware\RouterMiddleware;
use Ihsan\Client\Platform\Middleware\TemplatingMiddleware;
use Ihsan\Client\Platform\Template\TemplateEngineInterface;
use Ihsan\Client\Platform\Template\TwigTemplateEngine;
use Stack\Builder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Route;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class Bootstrap
{
    /**
     * @var Builder
     */
    private $stackPhp;

    /**
     * @var \SplPriorityQueue
     */
    private $middlewares;

    /**
     * @param array $middlewares
     */
    public function __construct(array $middlewares = array())
    {
        $this->stackPhp = new Builder();
        $this->middlewares = new \SplPriorityQueue();

        foreach ($middlewares as $middleware) {
            if (array_key_exists('class', $middleware)) {
                throw new \OutOfRangeException('index "class" not found.');
            }

            $parameters = array();
            $priority = 0;
            if (array_key_exists('parameters', $middleware) && is_array($middleware['parameters'])) {
                $parameters = $middleware['parameters'];
            }

            if (array_key_exists('priority', $middleware)) {
                $priority = (int) $middleware['priority'];
            }

            $this->addMiddleware($middleware['class'], $parameters, $priority);
        }
    }

    /**
     * @param string $middleware
     * @param array $parameters
     * @param int $priority
     */
    private function addMiddleware($middleware, array $parameters = array(), $priority = 0)
    {
        $this->middlewares->insert(array_merge(array($middleware), $parameters), $priority);
    }

    /**
     * @param Request $request
     * @param array $configs
     *
     * @return Response
     */
    public function handle(Request $request, array $configs)
    {
        $eventDipatcher = new EventDispatcher();
        $kernel = new Kernel($eventDipatcher, $configs);

        $this->addMiddleware(ConfigurationMiddleware::class, array($configs), 2049);
        $this->addMiddleware(RouterMiddleware::class, array(), 2047);
        $this->addMiddleware(EventDispatcherMiddleware::class, array($eventDipatcher), 2047);
        $this->addMiddleware(TemplatingMiddleware::class, array(), 2045);
        $this->addMiddleware(ApiClientMiddleware::class, array(), 2043);

        foreach ($this->middlewares as $middleware) {
            call_user_func_array(array($this->stackPhp, 'push'), $middleware);
        }

        $app = $this->stackPhp->resolve($kernel);
        $response = $app->handle($request);

        return $response->send();
    }
}
