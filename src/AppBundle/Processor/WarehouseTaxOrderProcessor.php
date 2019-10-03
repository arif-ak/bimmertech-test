<?php

namespace AppBundle\Processor;

use AppBundle\Entity\Order;
use AppBundle\Entity\TaxCategory;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WarehouseTaxOrderProcessor
 * @package AppBundle\Processor
 */
class WarehouseTaxOrderProcessor implements OrderProcessorInterface
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
     * @param OrderInterface $order
     */
    public function process(OrderInterface $order): void
    {
        /** @var Order $order */
        if ($order->getHasTax() && !$order->getVatNumber()) {
            $this->addTax($order);
        } elseif ($order->getVatNumber()) {
            if (!$this->validVatNumber($order->getVatNumber())) {
                $this->addTax($order);
            }
        }
    }

    /**
     * @param string $vatNumber
     * @return boolean
     */
    private function validVatNumber(string $vatNumber)
    {
        $checker = $this->container->get('app.service.check_vat_number');
        return $checker->ifVatNumberValid($vatNumber);
    }

    /**
     * @param Order $order
     */
    private function addTax(Order $order)
    {
        $taxAdjustments = $order->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        if (!$taxAdjustments->count() > 0) {
            $taxRate = null;
            $taxCategories = $order->getWarehouse()->getTaxCategory();
            /** @var TaxCategory $taxCategory */
            foreach ($taxCategories as $taxCategory) {
                foreach ($taxCategory->getRates() as $rate) {
                    $shipCountryCode = $order->getCountryCode();
                    /** @var ZoneMemberInterface $member */
                    foreach ($rate->getZone()->getMembers() as $member) {
                        if ($member->getCode() === $shipCountryCode) {
                            $taxRate = $rate;
                            break;
                        }
                    }
                }
            }
            if ($taxRate) {
                /** @var AdjustmentInterface $adjustment */
                $adjustment = new Adjustment();
                $adjustment->setType(AdjustmentInterface::TAX_ADJUSTMENT);
                $taxAdj = $order->getTotal() * $taxRate->getAmount();
                $adjustment->setAmount($taxAdj);
                $adjustment->setNeutral(false);
                $adjustment->setLabel('Tax that belong to warehouse');
                $order->addAdjustment($adjustment);
                $order->recalculateAdjustmentsTotal();
            }
        }
    }
}