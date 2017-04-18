<?php

namespace Ihsan\Client\Platform\Middleware;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class MiddlewareBuilder
{
    /**
     * @var \SplPriorityQueue
     */
    private $middlewares;

    public function __construct()
    {
        $this->middlewares = new \SplPriorityQueue();
    }

    /**
     * @param string $middlewareClass
     * @param array  $parameters
     * @param int    $priority
     */
    public function addMiddleware($middlewareClass, array $parameters = [], $priority = 0)
    {
        $this->middlewares->insert(array_merge([$middlewareClass], $parameters), $priority);
    }

    /**
     * @return \SplPriorityQueue
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}
