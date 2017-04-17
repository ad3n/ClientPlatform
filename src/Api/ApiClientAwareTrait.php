<?php

namespace Ihsan\Client\Platform\Api;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
trait ApiClientAwareTrait
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $url
     * @param array $options
     *
     * @return Response
     */
    public function get($url, array $options)
    {
        return $this->client->get($url, $options);
    }

    /**
     * @param $url
     * @param array $options
     *
     * @return Response
     */
    public function post($url, array $options)
    {
        return $this->client->post($url, $options);
    }

    /**
     * @param $url
     * @param array $options
     *
     * @return Response
     */
    public function put($url, array $options)
    {
        return $this->client->put($url, $options);
    }

    /**
     * @param $url
     * @param array $options
     *
     * @return Response
     */
    public function delete($url, array $options)
    {
        return $this->client->delete($url, $options);
    }
}
