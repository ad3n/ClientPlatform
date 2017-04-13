<?php

namespace Ihsan\Client\Platform\Template;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
interface TemplateEngineInterface
{
    /**
     * @param string $view
     * @param array  $variables
     *
     * @return string
     */
    public function render($view, array $variables = array());

    /**
     * @param string $view
     * @param array  $variables
     *
     * @return Response
     */
    public function renderResponse($view, array $variables = array());
}
