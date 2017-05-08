<?php

namespace Ihsan\Client\Platform\Middleware;

use Ihsan\Client\Platform\DependencyInjection\ContainerAwareInterface;
use Pimple\Container;
use Stack\StackedHttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class MiddlewareStack
{
    /**
     * @var \SplStack
     */
    private $specs;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->specs = new \SplStack();
        $this->container = $container;
    }

    public function unshift(/*$kernelClass, $args...*/)
    {
        if (func_num_args() === 0) {
            throw new \InvalidArgumentException('Missing argument(s) when calling unshift');
        }

        $spec = func_get_args();
        $this->specs->unshift($spec);

        return $this;
    }

    public function push(/*$kernelClass, $args...*/)
    {
        if (func_num_args() === 0) {
            throw new \InvalidArgumentException('Missing argument(s) when calling push');
        }

        $spec = func_get_args();
        $this->specs->push($spec);

        return $this;
    }

    public function resolve(HttpKernelInterface $app)
    {
        $middlewares = array($app);

        foreach ($this->specs as $spec) {
            $args = $spec;
            $firstArg = array_shift($args);

            if (is_callable($firstArg)) {
                $app = $firstArg($app);
            } else {
                $kernelClass = $firstArg;
                array_unshift($args, $app);

                $reflection = new \ReflectionClass($kernelClass);
                $app = $reflection->newInstanceArgs($args);
            }

            array_unshift($middlewares, $app);

            if ($app instanceof ContainerAwareInterface) {
                $app->setContainer($this->container);
            }
        }

        return new StackedHttpKernel($app, $middlewares);
    }
}
