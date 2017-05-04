<?php

namespace Ihsan\Client\Platform\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class ControllerResolver
{
    /**
     * @var UrlMatcher
     */
    private $urlMatcher;

    /**
     * @param UrlMatcher $urlMatcher
     */
    public function __construct(UrlMatcher $urlMatcher)
    {
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function resolve(Request $request)
    {
        $attributes = $this->urlMatcher->match($request->getPathInfo());
        $controllerNotation = explode('@', $attributes['_route']);
        unset($attributes['_route']);

        $controller = explode(':', $controllerNotation[0]);

        $total = count($controller);
        $last = $total - 1;

        $controller[$total] = sprintf('%sController', $controller[$last]);
        $controller[$last] = 'Controller';

        $action = sprintf('%sAction', $controllerNotation[1]);
        unset($controllerNotation);

        $class = implode('\\', $controller);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not exist.', $class));
        }

        $implements = class_implements($class);
        if (!array_key_exists(ControllerInterface::class, $implements)) {
            throw new \InvalidArgumentException(sprintf('"%s" must implement "%s"', $class, ControllerInterface::class));
        }

        return array(
            '_controller' => $class, //controller must an object
            '_action' => $action,
            '_parameters' => $attributes,
        );
    }
}
