<?php

namespace Ihsan\Client\Platform\Template;

/**
 * @author Muhamad Surya Iksanudin <surya.kejawen@gmail.com>
 */
interface TemplateAwareInterface
{
    public function setTemplate(TemplateEngineInterface $templateEngine);
}
