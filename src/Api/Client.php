<?php

namespace Ihsan\Client\Platform\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class Client implements ClientInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $paramKey;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @param Session $session
     * @param string  $baseUrl
     * @param string  $apiKey
     * @param string  $paramKey
     */
    public function __construct(Session $session, $baseUrl, $apiKey, $paramKey = 'api_key')
    {
        $this->session = $session;
        $this->baseUrl = $baseUrl;
        $this->paramKey = $paramKey;
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $token
     */
    public function bearer($token)
    {
        $this->addHeader('Authorization', sprintf('Bearer %s', $token));
    }

    /**
     * @param mixed $key
     * @param mixed $param
     */
    public function addHeader($key, $param)
    {
        $this->headers[$key] = $param;
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
        if ($token = $this->fetch('token')) {
            $this->bearer($token);
        }
        $this->addHeader('Content-Type', 'application/ld+json');
        $this->addHeader('Accept', 'application/ld+json');

        return $this->convertToSymfonyResponse(\Requests::get(sprintf('%s.jsonld?%s', $this->getRealUrl($url), http_build_query(array_merge([$this->paramKey => $this->apiKey], $options))), $this->headers, []));
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function post($url, array $options = [])
    {
        if ($token = $this->fetch('token')) {
            $this->bearer($token);
        }
        $this->addHeader('Content-Type', 'application/json');
        $this->addHeader('Accept', 'application/json');

        return $this->convertToSymfonyResponse(\Requests::post(sprintf('%s.json?%s=%s', $this->getRealUrl($url), $this->paramKey, $this->apiKey), $this->headers, json_encode($options)));
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function put($url, array $options = [])
    {
        if ($token = $this->fetch('token')) {
            $this->bearer($token);
        }
        $this->addHeader('Content-Type', 'application/json');
        $this->addHeader('Accept', 'application/json');

        return $this->convertToSymfonyResponse(\Requests::put(sprintf('%s.json?%s=%s', $this->getRealUrl($url), $this->paramKey, $this->apiKey), $this->headers, json_encode($options)));
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function patch($url, array $options = [])
    {
        if ($token = $this->fetch('token')) {
            $this->bearer($token);
        }
        $this->addHeader('Content-Type', 'application/json');
        $this->addHeader('Accept', 'application/json');

        return $this->convertToSymfonyResponse(\Requests::patch(sprintf('%s.json?%s=%s', $this->getRealUrl($url), $this->paramKey, $this->apiKey), $this->headers, json_encode($options)));
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     */
    public function delete($url, array $options = [])
    {
        if ($token = $this->fetch('token')) {
            $this->bearer($token);
        }
        $this->addHeader('Content-Type', 'application/ld+json');
        $this->addHeader('Accept', 'application/ld+json');

        return $this->convertToSymfonyResponse(\Requests::delete(sprintf('%s.jsonld?%s=%s', $this->getRealUrl($url), $this->paramKey, $this->apiKey), $this->headers, []));
    }

    /**
     * @param \Requests_Response $response
     *
     * @return Response
     */
    private function convertToSymfonyResponse(\Requests_Response $response)
    {
        return new Response(
            $response->body,
            $response->status_code,
            $response->headers->getAll()
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
