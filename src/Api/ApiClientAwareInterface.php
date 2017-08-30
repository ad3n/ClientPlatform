<?php

namespace Ihsan\Client\Platform\Api;

/**
 * @author Muhamad Surya Iksanudin <surya.kejawen@gmail.com>
 */
interface ApiClientAwareInterface
{
    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client);
}
