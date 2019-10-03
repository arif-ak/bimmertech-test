<?php

namespace AppBundle\Service;

use AppBundle\Component\OrderCheckoutTransitionsTaxes;
use AppBundle\Entity\Order;
use AppBundle\Entity\TaxCategory;
use Doctrine\ORM\EntityManager;
use SM\Factory\FactoryInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CheckoutTaxes
 * @package AppBundle\Service
 */
class CheckoutTaxes
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * CheckoutWarehouse constructor.
     * @param EntityManager $em
     */
    public function __construct(ContainerInterface $container, FactoryInterface $stateMachineFactory)
    {
        $this->container = $container;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * function for check and added Adjustment is taxes exist
     *
     * @param Order $resource
     */
    public function checkSetTaxes(Order $resource)
    {
        $warehouse = $resource->getWarehouse();
        $resource->setHasTax(false);

        /** @var TaxCategory $taxCategory */
        foreach ($warehouse->getTaxCategory() as $taxCategory) {
            /** @var TaxRateInterface $rate */
            foreach ($taxCategory->getRates() as $rate) {
                $shipCountryCode = $resource->getCountryCode();
                $isMember = false;
                /** @var ZoneMemberInterface $member */
                foreach ($rate->getZone()->getMembers() as $member) {
                    if ($member->getCode() === $shipCountryCode) {
                        $isMember = true;
                        break;
                    }
                }
                if ($isMember) {
                    $resource->setHasTax(true);
                    break;
                }
            }
        }
    }

    /**
     * @param Order $order
     */
    public function resolve(Order $order): void
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        if (!$order->getHasTax() && $stateMachine->can(OrderCheckoutTransitionsTaxes::TRANSITION_SKIP_TAXES)) {
            $stateMachine->apply(OrderCheckoutTransitionsTaxes::TRANSITION_SKIP_TAXES);
        }
    }
}