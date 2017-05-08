<?php

namespace Ihsan\Client\Platform\Event;

use Ihsan\Client\Platform\DependencyInjection\ContainerAwareInterface;
use Ihsan\Client\Platform\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class EventDispatcherMiddleware implements HttpKernelInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

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
        $this->eventDispatcher = $this->container['internal.event_dispatcher'];
        foreach ($this->container['event_listeners'] as $config) {
            try {
                $class = $this->container[$config['class']];
            } catch (\Exception $exception) {
                $class = new $config['class']();
            }

            $this->attach($config['event'], [$class, $config['method']], $config['priority']);
        }

        return $this->app->handle($request, $type, $catch);
    }

    /**
     * @param string   $event
     * @param callable $callback
     * @param int      $priority
     */
    private function attach($event, $callback, $priority = 0)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not callable.'));
        }

        $this->eventDispatcher->addListener($event, $callback, $priority);
    }
}
