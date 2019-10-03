<?php

namespace AppBundle\Service;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderInterface;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\ProductVariant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SupportBoardService
 * @package AppBundle\Service
 */
class SupportBoard
{
    const SUCCESS_MESSAGE = 'Success sending mail to customer';
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var SenderInterface
     */
    private $sender;
    /**
     * @var Container
     */
    private $container;

    /**
     * SupportBoardService constructor.
     * @param EntityManagerInterface $em
     * @param SenderInterface $sender
     * @param Container $container
     */
    public function __construct(EntityManagerInterface $em, SenderInterface $sender, Container $container)
    {
        $this->em = $em;
        $this->sender = $sender;
        $this->container = $container;
    }

    /**
     * @param $sendingItems
     * @return array
     */
    public function sendInstruction($sendingItems, $email)
    {
        try {
            $this->sender->send('instruction_added_notification',
                [$email], ['items' => $sendingItems]);
            foreach ($sendingItems as $item) {
                /** @var OrderItem $orderItem */
                $orderItem = $item['item'];
                $orderItem->setSupportStatus(OrderItemInterface::COMPLETE);
                $this->em->persist($orderItem);
            }

            $this->em->flush();
            return ['status' => 'success',
                'message' => self::SUCCESS_MESSAGE];
        } catch (Exception $exception) {
            return ['status' => 'error',
                'message' => $exception->getMessage()];
        }
    }

    /**
     * @param Order $order
     */
    public function supportStatus(Order $order)
    {
        $statusComplete = false;
        /** @var OrderItem $item */
        foreach ($order->getItems() as $item) {
            if ($item->getSupportStatus() !== OrderItemInterface::COMPLETE ||
                $item->getSupportStatus() !== OrderItemInterface::NOT_REQUIRED
            ) {
                $statusComplete = true;
                break;
            }
        }

        if ($statusComplete) {
            $order->setSupportStatus(OrderInterface::STATUS_COMPLETE);
        } else {
            $order->setSupportStatus(OrderInterface::STATUS_PARTIALLY_ADDED);
        }
        $this->em->persist($order);
        $this->em->flush();
    }

    public function isOrderItemsArePhysicalAndInstructionRequired($order)
    {
        /** @var Order $order */
        $items = $order->getItems();

        $orderItemsList = [];
        /** @var OrderItem $item */
        foreach ($items as $item) {
            $productVariant = $item->getVariant();
            if ($this->container->get('app.service.order_item_board_type_service')->
            isOrderItemHasRequireInstruction($item)
            ) {
                $orderItemsList[] = $item;
            }
        }

        return $orderItemsList;
    }

    /**
     * @param $sendingItems
     * @param $email
     * @return JsonResponse
     */
    public function sendInstructionAPI($sendingItems, $email)
    {
        try {
            $this->sender->send('instruction_added_notification', [$email], ['items' => $sendingItems]);
        } catch (Exception $exception) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
    }
}
