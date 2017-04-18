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
class TemplatingMiddleware implements HttpKernelInterface, ContainerAwareMiddlewareInterface
{
    use ContainerAwareMiddlewareTrait;

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
     * @param int     $type
     * @param bool    $catch
     *
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $configurations = $configs = $this->container['config'];

        $viewPath = sprintf('%s%s', $configurations['project_dir'], $configurations['template']['path']);
        $cachePath = sprintf('%s%s', $configurations['project_dir'], $configurations['template']['cache_dir']);

        $templateEngine = new TwigTemplateEngine($viewPath, $cachePath);
        $controller = $request->attributes->get('_controller');
        if ($controller instanceof TemplatingAwareInterface) {
            $controller->setTemplateEngine($templateEngine);
        }

        return $this->app->handle($request, $type, $catch);
    }
}
