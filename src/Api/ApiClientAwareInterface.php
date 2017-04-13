<?php

namespace Ihsan\Client\Platform\Api;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
interface ApiClientAwareInterface
{
    public function setClient(ClientInterface $client);
}
