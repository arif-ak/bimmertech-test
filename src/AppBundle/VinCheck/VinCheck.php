<?php

namespace AppBundle\VinCheck;

use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VinCheck
 * @package AppBundle\VinCheck
 */
class VinCheck
{
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
     * @return \GuzzleHttp\Client
     */
    private function getPartClient()
    {
        /** @var \GuzzleHttp\Client $client */
        $client = $this->container->get('eight_points_guzzle.client.api_vincheck_part');
        return $client;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    private function getProductClient()
    {
        /** @var \GuzzleHttp\Client $client */
        $client = $this->container->get('eight_points_guzzle.client.api_vincheck_products');
        return $client;
    }

    /**
     * @param string $vin
     * @param int $productId
     * @return bool
     */
    public function checkByProduct(string $vin, int $productId)
    {
        $client = $this->getPartClient();
        $url = $this->container->getParameter("vincheck_uri_part");
        $data = $this->send($client, $url, ['vin' => $vin, 'productId' => $productId]);

        // TODO: maybe we will need change logic
        if ($data['parts']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get vin data for vin product id
     *
     * @param string $vin
     * @param int $productId
     * @return mixed
     */
    public function getDataByProductId(string $vin, int $productId)
    {
        $client = $this->getPartClient();
        $url = $this->container->getParameter("vincheck_uri_part");
        return $this->send($client, $url, ['vin' => $vin, 'productId' => $productId]);
    }

    /**
     * @param Client $client
     * @param string $url
     * @param array $data
     * @return mixed
     */
    private function send(Client $client, string $url, array $data)
    {
        $data['service'] = $this->container->getParameter("site_name");

        try {
            $response = $client->post($url, ['form_params' => $data]);

            $response = $response->getBody()->getContents();

            return json_decode($response, true);
        } catch (\Exception $exception) {
            return [
                'error' => sprintf('Vin check service returned 500 error. For vin =  %s, vinProductId = %s ', $data['vin'], $data['productId'])
            ];
        }
    }

    /**
     * @param Client $client
     * @param string $url
     * @return array|mixed
     */
    private function sendGet(Client $client, string $url)
    {
        $data['service'] = $this->container->getParameter("site_name");

        try {
            $response = $client->get($url);

            $response = $response->getBody()->getContents();

            return json_decode($response, true);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string $vin
     * @return mixed
     */
    public function getCompatibilityProducts(string $vin)
    {
        $client = $this->getProductClient();
        $url = $this->container->getParameter("vincheck_uri_products");
        $result = $this->send($client, $url, ['vin' => $vin]);

        return $result;
    }

    /**
     * @param string $model
     * @param string $prodDate
     * @return mixed
     */
    public function getManualProducts(string $model, string $year)
    {
        $postParams = ['vinData' => [
            'E series' => $model,
            'Prod.date' => $year . '-01-01'
        ],

            'vin' => '- no vin Model: ' . $model . ' Year: ' . $year
        ];
        $client = $this->getProductClient();
        $url = $this->container->getParameter("vincheck_uri_manual");
        $result = $this->send($client, $url, $postParams);

        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getVinCheckProductModel($id)
    {
        $url = sprintf($this->container->getParameter("vincheck_uri_product_models"), $id);
        $client = $this->getProductClient();
        $result = $this->sendGet($client, $url);

        return $result;

    }
}