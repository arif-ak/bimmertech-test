<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\Warehouse;
use AppBundle\Entity\Zone;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

/**
 * Class CheckoutWarehouse
 * @package AppBundle\Service
 */
class CheckoutWarehouse
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * CheckoutWarehouse constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * function for adding warehouses to order items and to order
     *
     * @param Order $resource
     */
    public function addWarehouse(Order $resource)
    {
        $warehouses = $this->em->getRepository(Warehouse::class)->findAll();
        $addWarehouse = false;
        $customCountry = $resource->getCountryCode();
        /** @var  Collection $products */
        $products = $resource->getItems();
        /** @var OrderItem $product */
        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                /** @var Zone $zone */
                $zone = $warehouse->getZone();
                if (isset($zone)) {
                    $zoneMembers = $zone->getMembers();
                    /** @var ZoneMemberInterface $member */
                    foreach ($zoneMembers as $member) {
                        if ($member->getCode() === $customCountry) {
                            $product->setWarehouse($warehouse);
                            $addWarehouse = true;
                            continue;
                        }
                    }
                }
            }
            if (!$addWarehouse) {
                /** @var ProductVariant $variant */
                $variant = $product->getVariant();
                $product->setWarehouse($variant->getDefaultWarehouse());
            }
        }

        if(count($products)){
            $orderWarehouse = $products[0]->getWarehouse();
            if ($products->count() > 1) {
                /** @var ProductVariant $variantFirst */
                $variantFirst = $products[0]->getVariant();
                foreach ($products as $product) {
                    /** @var ProductVariant $variant */
                    $variant = $product->getVariant();
                    $productPriority = $variant->getPriority();
                    if ($productPriority > $variantFirst->getPriority()) {
                        $orderWarehouse = $variant->getDefaultWarehouse();
                    }
                }
            }
            $resource->setWarehouse($orderWarehouse);
        }
    }
}