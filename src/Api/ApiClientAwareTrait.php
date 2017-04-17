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
     * @param string $token
     */
    public function bearer($token)
    {
        $this->client->bearer($token);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function store($key, $value)
    {
        $this->client->store($key, $value);
    }

    /**
     * @param mixed $key
     * @param mixed $default
     */
    public function fetch($key, $default = null)
    {
        $this->client->fetch($key, $default);
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
