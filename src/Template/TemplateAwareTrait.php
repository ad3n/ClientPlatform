<?php

namespace Ihsan\Client\Platform\Template;

/**
 * @author Muhamad Surya Iksanudin <surya.kejawen@gmail.com>
 */
trait TemplateAwareTrait
{
    /**
     * @var TemplateEngineInterface
     */
    protected $templateEngine;

    /**
     * @param TemplateEngineInterface $templateEngine
     */
    public function setTemplate(TemplateEngineInterface $templateEngine)
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
