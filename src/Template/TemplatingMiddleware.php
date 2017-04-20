<?php

namespace Ihsan\Client\Platform\Template;

use Ihsan\Client\Platform\Middleware\ContainerAwareMiddlewareInterface;
use Ihsan\Client\Platform\Middleware\ContainerAwareMiddlewareTrait;
use Ihsan\Client\Platform\Twig\TwigFilterExtension;
use Ihsan\Client\Platform\Twig\TwigTemplateEngine;
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
        $config = $this->container['config'];
        if ($config['template_engine']) {
            $viewPath = sprintf('%s%s', $config['project_dir'], $config['template']['path']);
            $cachePath = sprintf('%s%s', $config['project_dir'], $config['template']['cache_dir']);

            $templateEngine = new $config['template_engine']($viewPath, $cachePath);
        } else {
            /** @var TwigTemplateEngine $templateEngine */
            $templateEngine = $this->container['internal.template'];
            $templateEngine->addExtension(new TwigFilterExtension());
        }

        $controller = $request->attributes->get('_controller');
        if ($controller instanceof TemplatingAwareInterface) {
            $controller->setTemplateEngine($templateEngine);
        }

        return $this->app->handle($request, $type, $catch);
    }
}
