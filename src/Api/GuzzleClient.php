<?php

namespace Ihsan\Client\Platform\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
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
    private $options = [];

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param Session $session
     * @param null    $baseUrl
     */
    public function __construct(Session $session, $baseUrl = null)
    {
        $this->session = $session;
        $this->guzzle = new Client();
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $token
     */
    public function bearer($token)
    {
        $this->options = array(
            'headers' => array(
                'Authorization' => sprintf('Bearer %s', $token),
            ),
        );
    }

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function fetch($key, $default = null)
    {
        return $this->session->get($key, $default);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function store($key, $value)
    {
        $this->session->set($key, $value);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function get($url, array $options = [])
    {
        try {
            $requestResponse = $this->guzzle->get(sprintf('%s%s', $this->baseUrl, $url), $this->mergeOptions($options));
        } catch (RequestException $exception) {
            $requestResponse = $exception->getResponse();
        }

        return $this->convertToSymfonyResponse($requestResponse);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function post($url, array $options = [])
    {
        try {
            $requestResponse = $this->guzzle->post(sprintf('%s%s', $this->baseUrl, $url), $this->mergeOptions(['form_params' => $options]));
        } catch (RequestException $exception) {
            $requestResponse = $exception->getResponse();
        }

        return $this->convertToSymfonyResponse($requestResponse);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function put($url, array $options = [])
    {
        try {
            $requestResponse = $this->guzzle->put(sprintf('%s%s', $this->baseUrl, $url), $this->mergeOptions(['form_params' => $options]));
        } catch (RequestException $exception) {
            $requestResponse = $exception->getResponse();
        }

        return $this->convertToSymfonyResponse($requestResponse);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function delete($url, array $options = [])
    {
        try {
            $requestResponse = $this->guzzle->delete(sprintf('%s%s', $this->baseUrl, $url), $this->mergeOptions($options));
        } catch (RequestException $exception) {
            $requestResponse = $exception->getResponse();
        }

        return $this->convertToSymfonyResponse($requestResponse);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function mergeOptions(array $options)
    {
        if ($token = $this->fetch('token')) {
            $this->bearer($token);
        }

        if (array_key_exists('headers', $options) && array_key_exists('headers', $this->options)) {
            $options['headers'] = array_merge($this->options['headers'], $options['headers']);
        } else {
            $options = array_merge($options, $this->options);
        }

        return array_merge($options, $this->fetch('_request_options', []));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Response
     */
    private function convertToSymfonyResponse(ResponseInterface $response)
    {
        return new Response(
            $response->getBody(),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
}
