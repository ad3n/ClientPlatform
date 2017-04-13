<?php

namespace Ihsan\Client\Platform\Api;

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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($url, array $options)
    {
        return $this->client->get($url, $options);
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($url, array $options)
    {
        return $this->client->post($url, $options);
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($url, array $options)
    {
        return $this->client->put($url, $options);
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete($url, array $options)
    {
        return $this->client->delete($url, $options);
    }
}
