<?php

namespace Ihsan\Client\Platform\Http;

use Ihsan\Client\Platform\Event\FilterController;
use Ihsan\Client\Platform\Event\FilterResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class Kernel implements HttpKernelInterface
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
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
        $controller = $request->attributes->get('_controller');
        if (method_exists($controller, 'getResponse') && $response = $controller->getResponse()) {//Early response
            return $response;
        }

        $filterResponse = new FilterResponse($request);
        $this->eventDispatcher->dispatch(KernelEvents::FILTER_REQUEST, $filterResponse);

        $response = $filterResponse->getResponse();
        if ($response instanceof Response) {
            return $response;
        }

        try {
            if (!$controller) {
                throw new \InvalidArgumentException('Controller is not valid.');
            }

            $action = $request->attributes->get('_action');
            if (!$action) {
                throw new \InvalidArgumentException('Action method is not valid.');
            }

            $request->attributes->remove('_controller');
            $request->attributes->remove('_action');
            $parameters = $request->attributes->get('_parameters', []);
            if (!empty($parameters)) {
                $request->attributes->remove('_parameters');
            }

            $filterController = new FilterController($controller);
            $this->eventDispatcher->dispatch(KernelEvents::FILTER_CONTROLLER, $filterController);

            $response = call_user_func_array([$controller, $action], array_merge($parameters, [$request]));
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not found!', Response::HTTP_NOT_FOUND);
        }

        if ($response instanceof Response) {
            $filterResponse->setResponse($response);
            $this->eventDispatcher->dispatch(KernelEvents::FILTER_RESPONSE, $filterResponse);

            return $filterResponse->getResponse();
        }

        throw new \InvalidArgumentException(sprintf('The controller must return a "\Symfony\Component\HttpFoundation\Response" object'));
    }
}
