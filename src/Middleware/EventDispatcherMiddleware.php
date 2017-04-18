<?php

namespace Ihsan\Client\Platform\Middleware;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class EventDispatcherMiddleware implements HttpKernelInterface, ContainerAwareMiddlewareInterface
{
    use ContainerAwareMiddlewareTrait;

    /**
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param HttpKernelInterface $app
     */
    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     *
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $configurations = $this->container['config'];
        foreach ($configurations['event_listeners'] as $config) {
            $this->attach($config['event'], $config['listener']);
        }

        $this->eventDispatcher = $this->container['internal.event_dispatcher'];

        return $this->app->handle($request, $type, $catch);
    }

    /**
     * @param string   $event
     * @param callable $callback
     */
    private function attach($event, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not callable.'));
        }

        $this->eventDispatcher->addListener($event, $callback);
    }
}
