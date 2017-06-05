<?php

namespace Ihsan\Client\Platform\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface as GuzzleResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@bisnis.com>
 */
class Client implements ClientInterface
{
    /**
     * @var string
     */
    private static $CLIENT_KEY = 'CLIENT_KEY';

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
     * @var GuzzleClient
     */
    private $request;

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

        if (!$this->session->has(static::$CLIENT_KEY)) {
            $clientKey = sha1(date('YmdHis'));
            $this->session->set(static::$CLIENT_KEY, $clientKey);
        } else {
            $clientKey = $this->session->get(static::$CLIENT_KEY);
        }
        $cookiesFile = sprintf('%s/%s.txt', sys_get_temp_dir(), $clientKey);

        $this->request = new GuzzleClient([
            'cookies' => new FileCookieJar($cookiesFile, true),
        ]);
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

    public function removeAll()
    {
        $sessions = $this->session->all();
        foreach ($sessions as $session) {
            $this->session->remove($session);
        }
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

        try {
            $response = $this->request->get(sprintf('%s.jsonld?%s', $this->getRealUrl($url), http_build_query(array_merge([$this->paramKey => $this->apiKey], $options))), [
                'headers' => $this->headers,
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        return $this->convertToSymfonyResponse($response);
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

        try {
            $response = $this->request->post(sprintf('%s.json?%s=%s', $this->getRealUrl($url), $this->paramKey, $this->apiKey), [
                'headers' => $this->headers,
                'body' => json_encode($options),
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        return $this->convertToSymfonyResponse($response);
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

        try {
            $response = $this->request->put(sprintf('%s.json?%s=%s', $this->getRealUrl($url), $this->paramKey, $this->apiKey), [
                'headers' => $this->headers,
                'body' => json_encode($options),
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        return $this->convertToSymfonyResponse($response);
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

        try {
            $response = $this->request->patch(sprintf('%s.json?%s=%s', $this->getRealUrl($url), $this->paramKey, $this->apiKey), [
                'headers' => $this->headers,
                'body' => json_encode($options),
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        return $this->convertToSymfonyResponse($response);
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

        try {
            $response = $this->request->delete(sprintf('%s.json?%s', $this->getRealUrl($url), http_build_query(array_merge([$this->paramKey => $this->apiKey], $options))), [
                'headers' => $this->headers,
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        return $this->convertToSymfonyResponse($response);
    }

    /**
     * @param GuzzleResponse $response
     *
     * @return Response
     */
    private function convertToSymfonyResponse(GuzzleResponse $response)
    {
        return new Response(
            $response->getBody()->getContents(),
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
