<?php

namespace Ihsan\Client\Platform\Template;

use Ihsan\Client\Platform\DependencyInjection\ContainerAwareInterface;
use Ihsan\Client\Platform\DependencyInjection\ContainerAwareTrait;
use Ihsan\Client\Platform\Twig\TwigFilterExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class TemplateEngineMiddleware implements HttpKernelInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

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
        $controller = $request->attributes->get('_controller');
        if ($controller instanceof TemplateAwareInterface) {
            $templateEngine = $this->container['internal.template'];
            if ($templateEngine instanceof \Twig_Environment) {
                $templateEngine->addExtension(new TwigFilterExtension());
            }

            $controller->setTemplate($templateEngine);
        }

        return $this->app->handle($request, $type, $catch);
    }
}