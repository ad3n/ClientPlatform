<?php

namespace Ihsan\Client\Platform\Template;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
interface TemplateAwareInterface
{
    public function setTemplate(TemplateEngineInterface $templateEngine);
}
