<?php

namespace Ihsan\Client\Platform\Template;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class TwigTemplateEngine implements TemplateEngineInterface
{
    /**
     * @var \Twig_Environment
     */
    private $engine;

    /**
     * @param string|null $templatePath
     * @param string|null $cachePath
     */
    public function __construct($templatePath = null, $cachePath = null)
    {
        if (null === $templatePath) {
            $templatePath = __DIR__.'/../var/views';
        }

        if (null === $cachePath) {
            $cachePath = __DIR__.'/../var/cache';
        }

        $loader = new \Twig_Loader_Filesystem($templatePath);
        $this->engine = new \Twig_Environment($loader, [
            'cache' => $cachePath,
        ]);
    }

    /**
     * @param string $view
     * @param array  $variables
     *
     * @return string
     */
    public function render($view, array $variables = [])
    {
        return $this->engine->render($view, $variables);
    }

    /**
     * @param string $view
     * @param array  $variables
     *
     * @return Response
     */
    public function renderResponse($view, array $variables = [])
    {
        return new Response($this->render($view, $variables));
    }
}
