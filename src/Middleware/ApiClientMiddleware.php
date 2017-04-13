<?php

namespace Ihsan\Client\Platform\Middleware;

use Ihsan\Client\Platform\Api\ApiClientAwareInterface;
use Ihsan\Client\Platform\Api\GuzzleClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class ApiClientMiddleware implements HttpKernelInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * @param HttpKernelInterface $app
     */
    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
    }

    /**
     * @param Request $request
     * @param int $type
     * @param bool $catch
     *
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $configurations = $request->attributes->get('_config', array());
        if (array_key_exists('http_client', $configurations)) {
            $httpClient = $configurations['http_client'];
        } else {
            $httpClient = new GuzzleClient();
        }

        $controller = $request->attributes->get('_controller');
        if ($controller instanceof ApiClientAwareInterface) {
            $controller->setClient($httpClient);
        }

        return $this->app->handle($request, $type, $catch);
    }
}
