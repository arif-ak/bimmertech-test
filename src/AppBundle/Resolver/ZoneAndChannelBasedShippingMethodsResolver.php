<?php

declare(strict_types=1);

namespace AppBundle\Resolver;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Webmozart\Assert\Assert;

class ZoneAndChannelBasedShippingMethodsResolver implements ShippingMethodsResolverInterface
{
    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var ZoneMatcherInterface
     */
    private $zoneMatcher;

    /**
     * @var ShippingMethodEligibilityCheckerInterface
     */
    private $eligibilityChecker;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param ZoneMatcherInterface $zoneMatcher
     * @param ShippingMethodEligibilityCheckerInterface $eligibilityChecker
     */
    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneMatcherInterface $zoneMatcher,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker
    )
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->zoneMatcher = $zoneMatcher;
        $this->eligibilityChecker = $eligibilityChecker;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject): array
    {
        /** @var ShipmentInterface $subject */
        Assert::true($this->supports($subject));
        /** @var OrderInterface $order */

        return $subject->getUnits()->first()->getOrderItem()->getWarehouse()->getShippingMethod()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ShippingSubjectInterface $subject): bool
    {
        return $subject instanceof ShipmentInterface &&
            null !== $subject->getOrder() &&
            null !== $subject->getOrder()->getShippingAddress() &&
            null !== $subject->getOrder()->getChannel();
    }
}
