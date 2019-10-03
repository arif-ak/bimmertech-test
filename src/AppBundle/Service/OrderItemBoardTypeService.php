<?php

namespace AppBundle\Service;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\DropDown;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserSupport;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderItemBoardTypeService
{
    const RETURNED = "returned";
    const NOT_RETURNED = "not_returned";


    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * LogisticBoardService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $em
    ) {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param $item
     * @return bool
     */
    public function checkIsOrderItemUnitOrOrderItemAndDropDownIsPhysical($item)
    {
        if ($item instanceof OrderItemUnit) {
            /** @var OrderItem $orderItem */
            /** @var OrderItemUnit $item */
            $orderItem = $item->getOrderItem();
            $product = $orderItem->getProduct();
            if (($orderItem->getType() !== OrderItemInterface::TYPE_WARRANTY &&
                    $product->getType() == ProductInterface::TYPE_PHYSICAL)
                ||
                ($orderItem->getType() !== OrderItemInterface::TYPE_WARRANTY &&
                    $product->getType() == ProductInterface::TYPE_CODING &&
                    $this->checkDropDropDownTypeIsPhysical($item))
                || ($orderItem->getType() !== OrderItemInterface::TYPE_WARRANTY &&
                    $product->getType() == ProductInterface::TYPE_USB_CODING &&
                    $this->checkDropDropDownTypeIsPhysical($item))
            ) {
                return true;
            } else {
                return false;
            }
        }

        if ($item instanceof OrderItem) {
            /** @var OrderItem $orderItem */
            $orderItem = $item;
            $product = $orderItem->getProduct();
            if (($orderItem->getType() !== OrderItemInterface::TYPE_WARRANTY &&
                    $product->getType() == ProductInterface::TYPE_PHYSICAL)
                ||
                ($orderItem->getType() !== OrderItemInterface::TYPE_WARRANTY &&
                    $product->getType() == ProductInterface::TYPE_CODING &&
                    $this->checkDropDropDownTypeIsPhysical($orderItem))
                || ($orderItem->getType() !== OrderItemInterface::TYPE_WARRANTY &&
                    $product->getType() == ProductInterface::TYPE_USB_CODING &&
                    $this->checkDropDropDownTypeIsPhysical($orderItem))
            ) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function checkDropDropDownTypeIsPhysical($item)
    {
        if ($item instanceof OrderItem) {
            $orderItem = $item;
        } elseif ($item instanceof OrderItemUnit) {
            /** @var OrderItem $orderItem */
            $orderItem = $item->getOrderItem();
        }

        /** @var ArrayCollection $dropDownList */
        $dropDownList = isset($orderItem) ? $orderItem->getOrderItemDropDownOptions() : [];
        if ($dropDownList->count() > 0) {
            /** @var DropDown $dropDown */
            foreach ($dropDownList as $dropDown) {
                if ($dropDown->getType() == DropDown::NONE_PRODUCT ||
                    $dropDown->getType() == DropDown::PHYSICAL_PRODUCT ||
                    $dropDown->getType() == DropDown::PHYSICAL_PRODUCT_WITH_CODDING
                ) {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * @param $orderItem
     * @return bool
     */
    public function checkIsOrderItemUnitIsUsbCoding($orderItem)
    {
        /** @var OrderItem $orderItem */
        $product = $orderItem->getProduct();
        if ($orderItem->getType() !== OrderItemInterface::TYPE_WARRANTY &&
            $product->getType() == ProductInterface::TYPE_USB_CODING
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function isCoddingItem($item)
    {
        // check coding status for order item
        /** @var ProductVariant $productVariant */
        $productVariant = $item->getVariant();
        $product = $item->getProduct();
        if (($item->getType() != OrderItemInterface::TYPE_WARRANTY &&
                $product->getType() == ProductInterface::TYPE_CODING)
            ||
            ($item->getType() != OrderItemInterface::TYPE_WARRANTY &&
                $productVariant->getHasSoftware() == true)
            ||
            ($item->getType() != OrderItemInterface::TYPE_WARRANTY &&
                $this->checkDropDropDownTypeIsCoding($item)
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function checkDropDropDownTypeIsCoding($orderItem)
    {
        /** @var OrderItem $orderItem */
        $dropDownList = $orderItem->getOrderItemDropDownOptions();
        if ($dropDownList->count() > 0) {
            /** @var DropDown $dropDown */
            foreach ($dropDownList as $dropDown) {
                if ($dropDown->getType() == DropDown::CODDING_PRODUCT ||
                    $dropDown->getType() == DropDown::PHYSICAL_PRODUCT_WITH_CODDING
                ) {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    public function isOrderItemUnitOrOrderItemReturned($item)
    {
        if ($item instanceof OrderItem) {
            /** @var OrderItem $item */
            $units = $item->getUnits();
            foreach ($units as $orderItemUnit) {
                $orderItemUnitReturned = $orderItemUnit->getOrderItemReturn();
                if ($orderItemUnitReturned) {
                    return true;
                }
            }
        }

        if ($item instanceof OrderItemUnit) {
            $orderItemUnit = $item;
            $orderItemUnitReturned = $orderItemUnit->getOrderItemReturn();
            if ($orderItemUnitReturned) {
                return true;
            }
        }

        return false;
    }

    public function isAllOrderItemReturned($item)
    {
        $returnedItems = [];

        if ($item instanceof OrderItem) {
            /** @var OrderItem $item */
            $units = $item->getUnits();
            foreach ($units as $orderItemUnit) {
                $orderItemUnitReturned = $orderItemUnit->getOrderItemReturn();
                if ($orderItemUnitReturned) {
                    $returnedItems[] = $this::RETURNED;
                } else {
                    $returnedItems[] = $this::NOT_RETURNED;
                }
            }
        }

        if (in_array($this::RETURNED, $returnedItems) &&
            !in_array($this::NOT_RETURNED, $returnedItems)) {
            return true;
        } elseif (in_array($this::RETURNED, $returnedItems) &&
            in_array($this::NOT_RETURNED, $returnedItems)) {
            return false;
        }

        return false;
    }

    public function isOrderHasShipment($order)
    {
        /** @var Order $order */
        $items = $order->getItems();

        /** @var OrderItem $item */
        foreach ($items as $item) {
            $product = $item->getProduct();
            if (!$this->isOrderItemUnitOrOrderItemReturned($item)) {
                if ($this->checkIsOrderItemUnitOrOrderItemAndDropDownIsPhysical($item)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isOrderItemHasRequireInstruction($item)
    {
        /** @var OrderItem $item */
        $productVariant = $item->getVariant();
        $product = $item->getProduct();
        if ($item->getType() != OrderItemInterface::TYPE_WARRANTY &&
            $product->getType() == ProductInterface::TYPE_PHYSICAL &&
            $productVariant->getInstructionRequired() == true) {
            return true;
        }

        return false;
    }

    public function getPersonalWithSupportOrder($order)
    {
        $personalUser = [];
        $logisticWarehouses = [];
        $logisticUserArray = [];
        $isNeedSupportPerson = false;
        $isNeedCodingPerson = false;

        $orderItemUnits = $this->em->getRepository(OrderItemUnit::class)
            ->getByShipmentAndWarehouse($order->getId());
        $orderItems = $order->getItems();

        /** @var OrderItemUnit $orderItemUnit */
        foreach ($orderItemUnits as $orderItemUnit) {
            $warehouse = $orderItemUnit->getWarehouse();
            if ($warehouse) {
                if (!$this->isOrderItemUnitHasTypeWarranty($orderItemUnit)) {
                    if ($this->container->get('app.service.order_item_board_type_service')->
                    checkIsOrderItemUnitOrOrderItemAndDropDownIsPhysical($orderItemUnit)) {
                        if (!in_array($warehouse->getId(), $logisticWarehouses)) {
                            $logisticWarehouses[] = $warehouse->getId();
                        }
                    }
                }
            }
        }

        foreach ($orderItems as $itemKey => $orderItem) {
            $warehouse = $orderItem->getWarehouse();
            if ($warehouse) {
                if ($this->container->get('app.service.order_item_board_type_service')
                    ->checkIsOrderItemUnitIsUsbCoding($orderItem)) {
                    if (!in_array($warehouse->getId(), $logisticWarehouses)) {
                        $logisticWarehouses[] = $warehouse->getId();
                    }
                }
            }

            if ($this->isOrderItemHasRequireInstruction($orderItem)) {
                $isNeedSupportPerson = true;
            }

            if ($this->isCoddingItem($orderItem)) {
                $isNeedCodingPerson = true;
            }
        }

        if (count($logisticWarehouses) > 0) {
            $logisticUserArray = $this->em->getRepository(UserLogistic::class)->createQueryBuilder('ul')
                ->andWhere('ul.warehouse IN (:warehouse)')
                ->setParameter('warehouse', $logisticWarehouses)
                ->getQuery()
                ->getResult();
        }

        $adminUsers = $this->em->getRepository(AdminUser::class)->findAll();
        foreach ($adminUsers as $user) {
            $role = $user->getRoles()[0];
            if ($role == AdminUser::DEFAULT_ADMIN_ROLE) {
                $personalUser[] = $user;
            }

            if ($isNeedCodingPerson && $role == UserCoding::CODING_ROLE) {
                $personalUser[] = $user;
            }

            if ($isNeedSupportPerson && $role == UserSupport::SUPPORT_ROLE) {
                $personalUser[] = $user;
            }
        }

        $orderPersonal = array_merge($logisticUserArray, $personalUser);

        return $orderPersonal;
    }

    public function isOrderItemUnitHasTypeWarranty($item)
    {
        $orderItem = $item;
        if ($item instanceof OrderItemUnit) {
            $orderItem = $item->getOrderItem();
        } elseif ($item instanceof OrderItem) {
            $orderItem = $item;
        }

        return $orderItem->getType() == OrderItemInterface::TYPE_WARRANTY ? true : false;
    }
}
