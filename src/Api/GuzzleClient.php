<?php

namespace Ihsan\Client\Platform\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class GuzzleClient implements ClientInterface
{
    /**
     * @var Client
     */
    private $guzzle;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var array
     */
    private $options = array();

    public function __construct()
    {
        $this->session = new Session();
        $this->session->start();
        $this->guzzle = new Client();
    }

    /**
     * @param string $token
     */
    public function bearer($token)
    {
        $this->options = array(
            'headers' => array(
                'Authorization' => sprintf('Bearer %s', $token),
            )
        );
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function fetch($key)
    {
        return $this->session->get($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function save($key, $value)
    {
        $this->session->set($key, $value);
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($url, array $options = array())
    {
        try {
            return $this->guzzle->get($url, $this->mergeOptions($options));
        } catch (RequestException $exception) {
            return $exception->getResponse();
        }
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($url, array $options = array())
    {
        try {
            return $this->guzzle->post($url, $this->mergeOptions($options));
        } catch (RequestException $exception) {
            return $exception->getResponse();
        }
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($url, array $options = array())
    {
        try {
            return $this->guzzle->put($url, $this->mergeOptions($options));
        } catch (RequestException $exception) {
            return $exception->getResponse();
        }
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete($url, array $options = array())
    {
        try {
            return $this->guzzle->delete($url, $this->mergeOptions($options));
        } catch (RequestException $exception) {
            return $exception->getResponse();
        }
    }

    /**
     * @param array $options
     * @return array
     */
    private function mergeOptions(array $options)
    {
        if (array_key_exists('headers', $options) && array_key_exists('headers', $this->options)) {
            $options['headers'] = array_merge($this->options['headers'], $options['headers']);
        } else {
            $options = array_merge($options, $this->options);
        }

        return $options;
    }
}
