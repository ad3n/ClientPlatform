<?php

namespace Ihsan\Client\Platform\Api;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
interface ClientInterface
{
    /**
     * @param string $token
     */
    public function bearer($token);

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function addHeader($key, $value);

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function fetch($key, $default = null);

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function store($key, $value);

    /**
     * @param mixed $key
     */
    public function remove($key);

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function get($url, array $options = []);

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function post($url, array $options = []);

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function put($url, array $options = []);

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function patch($url, array $options = []);

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function delete($url, array $options = []);
}
