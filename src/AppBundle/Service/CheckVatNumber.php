<?php

namespace AppBundle\Service;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CheckVatNumber
 * @package AppBundle\Service
 */
class CheckVatNumber
{
    const URI_VALIDATE_VAT = '/api/validate';
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * VinCheck constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $vatNumber
     * @return bool|\Psr\Http\Message\ResponseInterface|string
     */
    public function checkVatNumber(string $vatNumber)
    {
        try {
            $response = $this->checking($vatNumber);
            if ($response['valid']) {
                return $response;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param string $vatNumber
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    private function checking(string $vatNumber)
    {
        /** @var \GuzzleHttp\Client $client */
        $client = $this->container->get('eight_points_guzzle.client.api_vat_number');
        $accessKey = $this->container->getParameter("apilayer_key");
        $uri = CheckVatNumber::URI_VALIDATE_VAT . '?access_key=' . $accessKey . '&vat_number=' . $vatNumber . '&format=1';
        $response = $client->get($uri);
        $response = $response->getBody()->getContents();
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * @param string $vatNumber
     * @return mixed
     */
    public function ifVatNumberValid(string $vatNumber)
    {
        try {
            $response = $this->checking($vatNumber);
            return $response['valid'];
        } catch (Exception $exception) {
            return false;
        }
    }
}