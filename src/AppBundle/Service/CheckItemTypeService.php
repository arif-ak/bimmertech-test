<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;

/**
 * Class CheckItemTypeService
 * @package AppBundle\Service
 */
class CheckItemTypeService
{

    const PRODUCT_LIST = 'product_list';
    const ITEM = 'item';
    const ADDONS = 'addons';
    const WARRANTY = 'warranty';
    const INCLUDE_ADDON = 'include_addon';
    const OPTIONS = 'options';

    /**
     * @param $object
     * @return array
     */
    public function checkType($object)
    {
        /** @var Order $object */
        $productList = [];
        foreach ($object->getItems() as $item) {
            /** @var OrderItem $item */
            if ($item->getType() === $this::ITEM) {
                $productList[] = [
                    $this::ITEM => $this->getItemProducts($item),
                    $this::OPTIONS => $this->getOptions($item),
                    $this::ADDONS => $this->getAddons($item),
                    $this::WARRANTY => $this->getWarranty($item)
                ];
            }
        }


        return $productList;
    }

    /**
     * @param OrderItem $item
     * @return array
     */
    private function getOptions(OrderItem $item)
    {
        $options = [];
        foreach ($item->getOrderItemDropDownOptions() as $dropDown) {
            /** @var OrderItemDropDownOption $dropDown */
            $options[] = [
                'name' => $dropDown->getDropDownOption()->getName()
            ];
        }
        return $options;
    }

    /**
     * @param OrderItem $item
     * @return array
     */
    private function getAddons(OrderItem $item)
    {
        $addons = [];
        /** @var OrderItem $addon */
        foreach ($item->getAddons() as $addon) {
            if ($addon->getType() === 'addon') {
                $addons[] = [
                    'name' => $addon->getProductName(),
                    'unitPrice' => $addon->getUnitPrice(),
                    'quantity' => $addon->getQuantity(),
                    'price' => $addon->getPrice()
                ];
            }
        }
        return $addons;
    }

    /**
     * @param OrderItem $item
     * @return array
     */
    private function getWarranty(OrderItem $item)
    {
        $warranty = [];
        /** @var OrderItem $addon */
        foreach ($item->getAddons() as $addon) {
            if ($addon->getType() === $this::WARRANTY) {
                $warranty[] = [
                    'name' => $addon->getProductName(),
                    'unitPrice' => $addon->getUnitPrice(),
                    'quantity' => $addon->getQuantity(),
                    'price' => $addon->getPrice()

                ];
            }
        }
        return $warranty;
    }

    /**
     * @param OrderItem $item
     * @return array
     */
    private function getItemProducts(OrderItem $item)
    {
        $products[] = [
            'name' => $item->getProductName(),
            'unitPrice' => $item->getUnitPrice(),
            'quantity' => $item->getQuantity(),
            'price' => $item->getPrice()
        ];

        return $products;
    }
}