<?php

namespace Ihsan\Client\Platform\Api;

use Psr\Http\Message\ResponseInterface;

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
     * @return mixed
     */
    public function fetch($key);

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function save($key, $value);

    /**
     * @param $url
     * @param array $options
     * @return ResponseInterface
     */
    public function get($url, array $options = array());

    /**
     * @param $url
     * @param array $options
     * @return ResponseInterface
     */
    public function post($url, array $options = array());

    /**
     * @param $url
     * @param array $options
     * @return ResponseInterface
     */
    public function put($url, array $options = array());

    /**
     * @param $url
     * @param array $options
     * @return ResponseInterface
     */
    public function delete($url, array $options = array());
}
