<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BoptController
 * @package AppBundle\Controller
 */
class BoptController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $productId = $request->get('productId');
        $vin = $request->get('vin');

        if (!$productId || !$vin) {
            return new Response('bad parameter ', 400);
        }

        $vincheck = $this->get('app.vin_check.vin_check');

        if (!$data = $vincheck->getDataByProductId($vin, $productId)) {
            return new Response('Data not found ', 404);
        }

        if (isset($data['error'])) {
            return new Response($data['error'], 400);
        }

        return $this->render('Bopt/bopt_index.html.twig', [
            'vin' => $vin,
            'productId' => $productId,
            'parts' => $data['parts'],
            'specification' => $this->getSpecification($data)
        ]);
    }

    /**
     * Prepare  specification data
     *
     * @param $data
     * @return mixed
     */
    private function getSpecification($data)
    {

        $options = '';

        foreach ($data['vinData']['Option'] as $value) {

            $options = $options . ' ' . key($value);
        }

        $data['vinData']['Option'] = $options;
        return $data['vinData'];
    }
}