<?php

namespace Ihsan\Client\Platform\EventListener;

use Ihsan\Client\Platform\Api\ApiClientAwareInterface;
use Ihsan\Client\Platform\DependencyInjection\ContainerAwareInterface;
use Ihsan\Client\Platform\DependencyInjection\ContainerAwareTrait;
use Ihsan\Client\Platform\Event\FilterController;
use Ihsan\Client\Platform\Template\TemplateAwareInterface;

/**
 * @author Muhamad Surya Iksanudin <surya.kejawen@gmail.com>
 */
class ControllerListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param FilterController $event
     */
    public function filterController(FilterController $event)
    {
        $controller = $event->getController();

        if ($controller instanceof TemplateAwareInterface) {
            $controller->setTemplate($this->container['internal.template']);
        }

        if ($controller instanceof ApiClientAwareInterface) {
            $controller->setClient($this->container['internal.http_client']);
        }

        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }
    }
}
