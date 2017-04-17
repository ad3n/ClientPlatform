<?php

namespace Ihsan\Client\Platform\Middleware;

use Bisnis\Middleware\ContainerAwareMiddlewareInterface;
use Bisnis\Middleware\ContainerAwareMiddlewareTrait;
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
     * @param EventDispatcher     $eventDispatcher
     */
    public function __construct(HttpKernelInterface $app, EventDispatcher $eventDispatcher)
    {
        $this->app = $app;
        $this->eventDispatcher = $eventDispatcher;
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
        $configurations = [];
        if (empty($configs = $this->container['config'])) {
            $configurations = $configs;
        }

        if (array_key_exists('event_listeners', $configurations)) {
            foreach ($configurations['event_listeners'] as $config) {
                if (!key_exists('event', $config)) {
                    throw new \OutOfBoundsException(sprintf('Key "event" must be set in "filter" key.'));
                }
                if (!key_exists('listener', $config)) {
                    throw new \OutOfBoundsException(sprintf('Key "listener" must be set in "filter" key.'));
                }

                $this->attach($config['event'], $config['listener']);
            }
        }

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
