<?php

namespace AppBundle\Controller;


use AppBundle\Repository\OrderRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HistoryController
 * @package AppBundle\Controller
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request, $id)
    {
        $history = $this->historyRepository->findBy(['order' => $id], ['id' => 'DESC']);
        if (!$order = $this->orderRepository->find($id)) {
            return $this->render('Order/History/404show.html.twig');
        }

        return $this->render('Order/History/show.html.twig', [
            'messages' => $history,
            'order' => $order
        ]);
    }
}