<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\CarModel;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCarModel;
use AppBundle\Entity\ProductVariant;
use AppBundle\Repository\CarModelRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CarModelController extends Controller
{

    /**
     * @var CarModelRepository
     */
    private $carModelRepository;

    /**
     * CarModelController constructor.
     * @param CarModelRepository $carModelRepository
     */
    public function __construct(CarModelRepository $carModelRepository)
    {
        $this->carModelRepository = $carModelRepository;
    }

    /**
     * Get all model
     *
     * @return JsonResponse
     */
    public function index()
    {
        $carModels = $this->carModelRepository->findAll();

        return new JsonResponse($carModels);
    }

    /**
     * Get model selected or vin number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getModel(Request $request)
    {
        if (!$vinData = $request->getSession()->get('vincheck')) {
            return new JsonResponse('Model not selected', JsonResponse::HTTP_NO_CONTENT);
        }
        
        $data = [
            'label' => $vinData['label'],
            'model' => $vinData['model'],
            'vinNumber'=>$vinData['vin'],
        ];

        return new JsonResponse($data);
    }

    /**
     * Check compatibility products by car model
     *
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkModel(Request $request)
    {

        if (!$model = $request->get('model')) {
            return new JsonResponse('Bad parameter model', 400);
        }

        /** @var CarModel $carModel */
        if (!$carModel = $this->get('app.repository.car_model')->findOneByCode($model)) {
            return new JsonResponse('This model not found', JsonResponse::HTTP_NO_CONTENT);
        }
        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());

        $this->get('app.vin_check.vin_check_session')->saveToSessionByManual($model, $carModel->getYear(), $channel, 'en_US', $carModel->getLabel());

        return new  JsonResponse($carModel->getLabel());
    }

    /**
     * Check compatibility products by car model
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkModelAndSave(Request $request, $id)
    {
        $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->findByIds([325, 326]);

        $result = $this->get('app.service.car_model')->carModelProductContainer($products);

        return new JsonResponse(['data' => $result], Response::HTTP_OK);
    }

    /**
     * Check compatibility products by vin number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkVin(Request $request)
    {
        if (!$vin = $request->get('vin')) {
            return new JsonResponse('Bad parameter vin', 400);
        }

        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());

        try {
            $session = $this->get('app.vin_check.vin_check_session')->saveToSessionByVin($vin, $channel);

            $data = [
                'vinNumber'=>$session->get('vincheck')['vin'],
                'label'=>$session->get('vincheck')['label']
            ];

            return new  JsonResponse($data);
        } catch (Exception $exception) {
            $this->addFlash('error', 'Something went wrong');
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getSessionParams(Request $request)
    {
        $session = new Session();
        $sessionProducts = $session->get('vincheck');

        return new JsonResponse(["session" => $sessionProducts]);
    }
}
