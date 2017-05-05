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
     * @param string  $baseUrl
     * @param string  $apiKey
     * @param string  $paramKey
     */
    public function __construct(Session $session, $baseUrl, $apiKey, $paramKey = 'api_key')
    {
        $this->session = $session;
        $this->guzzle = new Client();
        $this->baseUrl = $baseUrl;
        $this->options['query'] = [$paramKey => $apiKey];
    }

    /**
     * @param string $token
     */
    public function bearer($token)
    {
        $this->options['headers'] = ['Authorization' => sprintf('Bearer %s', $token)];
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
     * @param mixed $key
     */
    public function remove($key)
    {
        $this->session->remove($key);
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
            $requestResponse = $this->guzzle->get($this->getRealUrl($url), $this->mergeOptions(['query' => $options]));
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
            $requestResponse = $this->guzzle->post($this->getRealUrl($url), $this->mergeOptions(['form_params' => $options]));
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
            $requestResponse = $this->guzzle->put($this->getRealUrl($url), $this->mergeOptions(['form_params' => $options]));
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
    public function patch($url, array $options = [])
    {
        return $this->put($url, $options);
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
            $requestResponse = $this->guzzle->delete($this->getRealUrl($url), $this->mergeOptions($options));
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

        if (array_key_exists('headers', $this->options)) {
            if (array_key_exists('headers', $options)) {
                $options['headers'] = array_merge($this->options['headers'], $options['headers']);
            } else {
                $options['headers'] = $this->options['headers'];
            }
        }

        if (array_key_exists('query', $options)) {
            $options['query'] = array_merge($this->options['query'], $options['query']);
        } else {
            $options['query'] = $this->options['query'];
        }

        return $options;
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

    /**
     * @param string $url
     *
     * @return string
     */
    private function getRealUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        } else {
            return sprintf('%s%s', $this->baseUrl, $url);
        }
    }
}
