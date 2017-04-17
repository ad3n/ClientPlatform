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
     *
     * @return mixed
     */
    public function fetch($key);

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function store($key, $value);

    /**
     * @param $url
     * @param array $options
     *
     * @return Response
     */
    public function get($url, array $options = []);

    /**
     * @param $url
     * @param array $options
     *
     * @return Response
     */
    public function post($url, array $options = []);

    /**
     * @param $url
     * @param array $options
     *
     * @return Response
     */
    public function put($url, array $options = []);

    /**
     * @param $url
     * @param array $options
     *
     * @return Response
     */
    public function delete($url, array $options = []);
}
