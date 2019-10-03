<?php

namespace AppBundle\Resolver;


use Payum\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Webmozart\Assert\Assert;

/**
 * Class ChannelBasedPaymentMethodsResolver
 * @package AppBundle\Resolver
 */
class ChannelBasedPaymentMethodsResolver implements PaymentMethodsResolverInterface
{
    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     */
    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedMethods(BasePaymentInterface $payment): array
    {

        Assert::true($this->supports($payment), 'This payment method is not support by resolver');

        /** @var Payment $payment */
        return $payment->getOrder()->getWarehouse()->getPaymentMethod()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function supports(BasePaymentInterface $payment): bool
    {
        return $payment instanceof PaymentInterface &&
            null !== $payment->getOrder() &&
            null !== $payment->getOrder()->getChannel();
    }
}
