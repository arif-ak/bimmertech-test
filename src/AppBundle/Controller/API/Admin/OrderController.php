<?php

namespace AppBundle\Controller\API\Admin;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserSupport;
use AppBundle\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class OrderController
 * @package AppBundle\Controller\API\Admin
 */
class OrderController extends Controller
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var
     */
    protected $securityContext;

    /**
     * OrderController constructor.
     * @param OrderRepository $orderRepository
     * @param AuthorizationCheckerInterface $securityContext
     */
    public function __construct(OrderRepository $orderRepository, AuthorizationCheckerInterface $securityContext)
    {
        $this->securityContext = $securityContext;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexRole(Request $request)
    {
        $role = $this->getRole();
        $orders = $this->orderRepository->getUserRoleOrders($role);

        if ($this->securityContext->isGranted(UserLogistic::LOGISTIC_ROLE)) {
            $orders = $this->sortLogistic($orders);
        }
        if ($this->securityContext->isGranted(UserCoding::CODING_ROLE)) {
            $orders = $this->sortCoding($orders);
        }
        if ($this->securityContext->isGranted(UserSupport::SUPPORT_ROLE)) {
            $orders = $this->sortSupport($orders);
        }
        return $this->render(':Order/Role:logistOrdes.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @param $orders
     * @return array
     */
    private function sortSupport($orders)
    {
        $collection = new ArrayCollection($orders);
        $iterator = $collection->filter(function ($order) {
            /** @var Order $order */
            if ($order->getSupportStatus() == 'not added' or $order->getSupportStatus() == 'partially added') {
                return $order;
            }
        })->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getId() < $b->getId()) ? -1 : 1;
        });

        $first = iterator_to_array($iterator);
        $iterator = $collection->filter(function ($order) {
            /** @var Order $order */
            if ($order->getSupportStatus() == 'completed') {
                return $order;
            }
        })->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getId() > $b->getId()) ? -1 : 1;
        });
        $last = iterator_to_array($iterator);
        return array_merge($first, $last);
    }

    /**
     * @param $orders
     * @return array
     */
    private function sortCoding($orders)
    {
        $collection = new ArrayCollection($orders);

        $iterator = $collection->filter(function ($order) {
            /** @var Order $order */
            if ($order->getCodingStatus() == 'not coded' or $order->getCodingStatus() == 'partially coded') {
                return $order;
            }
        })->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getId() < $b->getId()) ? -1 : 1;
        });

        $first = iterator_to_array($iterator);
        $iterator = $collection->filter(function ($order) {
            /** @var Order $order */
            if ($order->getCodingStatus() == 'completed') {
                return $order;
            }
        })->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getId() > $b->getId()) ? -1 : 1;
        });
        $last = iterator_to_array($iterator);
        return array_merge($first, $last);
    }

    /**
     * @param $orders
     * @return array
     */
    private function sortLogistic($orders)
    {
        $collection = (new ArrayCollection($orders))->filter(function ($order) {
            /** @var Order $order */

            $items = $order->getItems()->filter(function ($item) {
                /** @var OrderItem $item */
                if ($item->getProduct()->getType() == null) {
                    return $item;
                }
            });

            if ($items->count() > 0) {
                return $order;
            }
        });

        $iterator = $collection->filter(function ($order) {
            /** @var Order $order */
            if ($order->getShippingState() == 'not shipped' or $order->getShippingState() == 'partially shipped') {
                return $order;
            }
        })->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getId() < $b->getId()) ? -1 : 1;
        });

        $first = iterator_to_array($iterator);
        $iterator = $collection->filter(function ($order) {
            /** @var Order $order */
            if ($order->getShippingState() == 'shipped') {
                return $order;
            }
        })->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getId() > $b->getId()) ? -1 : 1;
        });

        $last = iterator_to_array($iterator);
        return array_merge($first, $last);
    }

    /**
     * @return string|null
     */
    public function getRole()
    {
        $role = null;
        if ($this->securityContext->isGranted('ROLE_ADMINISTRATION_ACCESS')) {
            $role = 'ROLE_ADMINISTRATION_ACCESS';
        }
        if ($this->securityContext->isGranted('ROLE_LOGISTIC_ACCESS_DUBAI')) {
            $role = 'ROLE_LOGISTIC_ACCESS_DUBAI';
        }
        if ($this->securityContext->isGranted('ROLE_LOGISTIC_ACCESS_WARSAW')) {
            $role = 'ROLE_LOGISTIC_ACCESS_WARSAW';
        }
        if ($this->securityContext->isGranted('ROLE_LOGISTIC_ACCESS_ORLANDO')) {
            $role = 'ROLE_LOGISTIC_ACCESS_ORLANDO';
        }
        if ($this->securityContext->isGranted(UserSupport::SUPPORT_ROLE)) {
            $role = UserSupport::SUPPORT_ROLE;
        }
        if ($this->securityContext->isGranted(UserCoding::CODING_ROLE)) {
            $role = UserCoding::CODING_ROLE;
        }
        return $role;
    }
}