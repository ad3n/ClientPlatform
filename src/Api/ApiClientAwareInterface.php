<?php

namespace Ihsan\Client\Platform\Api;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
interface ApiClientAwareInterface
{
    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client);
}
