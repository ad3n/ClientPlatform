<?php

namespace Ihsan\Client\Platform\Controller;

use Ihsan\Client\Platform\Api\ApiClientAwareInterface;
use Ihsan\Client\Platform\Api\ApiClientAwareTrait;
use Ihsan\Client\Platform\Template\TemplatingAwareInterface;
use Ihsan\Client\Platform\Template\TemplatingAwareTrait;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
abstract class AbstractController implements ControllerInterface, TemplatingAwareInterface, ApiClientAwareInterface
{
    use TemplatingAwareTrait;
    use ApiClientAwareTrait;
}
