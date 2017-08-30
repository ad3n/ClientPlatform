<?php

namespace Ihsan\Client\Platform\Controller;

use Ihsan\Client\Platform\Api\ApiClientAwareInterface;
use Ihsan\Client\Platform\Api\ApiClientAwareTrait;
use Ihsan\Client\Platform\Template\TemplateAwareInterface;
use Ihsan\Client\Platform\Template\TemplateAwareTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhamad Surya Iksanudin <surya.kejawen@gmail.com>
 */
abstract class AbstractController implements ControllerInterface, TemplateAwareInterface, ApiClientAwareInterface
{
    use TemplateAwareTrait;
    use ApiClientAwareTrait;

    /**
     * @param string $url
     * @param string $method
     * @param array  $data
     *
     * @return Response
     */
    protected function request($url, $method = 'GET', array $data = [])
    {
        try {
            return call_user_func_array([$this, strtolower($method)], [$url, $data]);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Http method is not valid.');
        }
    }

    /**
     * @param string $token
     */
    protected function auth($token)
    {
        $this->client->bearer($token);
    }
}
