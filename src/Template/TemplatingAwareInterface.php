<?php

namespace Ihsan\Client\Platform\Template;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
interface TemplatingAwareInterface
{
    public function setTemplateEngine(TemplateEngineInterface $templateEngine);
}
