<?php

namespace Ihsan\Client\Platform\Middleware;

use Ihsan\Client\Platform\Template\TemplatingAwareInterface;
use Ihsan\Client\Platform\Template\TwigTemplateEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class TemplatingMiddleware implements HttpKernelInterface
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

        if (array_key_exists('template', $configurations)) {
            $path = null;
            $cache = null;

            if (array_key_exists('path', $configurations['template'])) {
                $path = $configurations['template']['path'];
            }

            if (array_key_exists('cache', $configurations['template'])) {
                $cache = $configurations['template']['cache'];
            }

            $templateEngine = new TwigTemplateEngine($path, $cache);
            $controller = $request->attributes->get('_controller');
            if ($controller instanceof TemplatingAwareInterface) {
                $controller->setTemplateEngine($templateEngine);
            }
        }

        return $this->app->handle($request, $type, $catch);
    }
}
