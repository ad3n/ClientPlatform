<?php

namespace Ihsan\Client\Platform\Template;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
trait TemplatingAwareTrait
{
    /**
     * @var TemplateEngineInterface
     */
    protected $templateEngine;

    /**
     * @param TemplateEngineInterface $templateEngine
     */
    public function setTemplateEngine(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param string $view
     * @param array  $variables
     *
     * @return string
     */
    protected function render($view, array $variables = array())
    {
        return $this->templateEngine->render($view, $variables);
    }

    /**
     * @param string $view
     * @param array  $variables
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderResponse($view, array $variables = array())
    {
        return $this->templateEngine->renderResponse($view, $variables);
    }
}
