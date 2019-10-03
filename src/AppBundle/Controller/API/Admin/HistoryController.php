<?php

namespace AppBundle\Controller\API\Admin;


use AppBundle\Entity\History;
use AppBundle\Repository\OrderRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class History
 * @package AppBundle\Controller\API\Admin
 */
class HistoryController extends Controller
{

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var EntityRepository
     */
    private $historyRepository;

    /**
     * HistoryController constructor.
     *
     * @param OrderRepository $orderRepository
     * @param EntityRepository $historyRepository
     */
    public function __construct(OrderRepository $orderRepository, EntityRepository $historyRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function create(Request $request, $id)
    {
        if (!$order = $this->get('sylius.repository.order')->find($id)) {
            return new JsonResponse('Order not found');
        }

        $user = $this->getUser();
        $history = new History();

        $history->setOrder($order);
        $history->setUser($user);
        $history->setMessage($request->get('message', 'no message'));

        $this->historyRepository->add($history);

        return new JsonResponse('Created', JsonResponse::HTTP_CREATED);
    }
}