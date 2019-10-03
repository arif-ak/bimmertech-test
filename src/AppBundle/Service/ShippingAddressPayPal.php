<?php

namespace AppBundle\Service;

use AppBundle\Entity\Address;
use AppBundle\Entity\Order;
use AppBundle\Entity\PaymentMethod;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ShippingAddressPayPal
 * @package AppBundle\Service
 */
class ShippingAddressPayPal
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ShippingAddressPayPal constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Order $order
     */
    public function setAddressPayPal(Order $order)
    {
        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $order->getLastPayment('completed')->getMethod();

        if ($paymentMethod->getGatewayConfig()->getFactoryName() === 'paypal_express_checkout') {

            $this->addAddress($order);

        }
    }

    /**
     * @param string $vatNumber
     * @return mixed
     */
    private function checkValid(string $vatNumber)
    {
        $checker = $this->container->get('app.service.check_vat_number');
        return $checker->ifVatNumberValid($vatNumber);
    }

    /**
     * @param Order $order
     * @param AddressInterface $address
     */
    private function addAddress(Order $order, AddressInterface $address = null)
    {
        $address = new Address();

        if ($customer = $order->getCustomer()) {
            $address->setPhoneNumber($customer->getPhoneNumber());
        }

        $paymentDetails = $order->getLastPayment('completed')->getDetails();


        $address->setFirstName($paymentDetails['FIRSTNAME']);
        $address->setLastName($paymentDetails['LASTNAME']);
        $address->setCountryCode($paymentDetails['SHIPTOCOUNTRYCODE']);
        $address->setCity($paymentDetails['SHIPTOCITY']);
        $address->setPostcode($paymentDetails['SHIPTOZIP']);
        $address->setStreet($paymentDetails['SHIPTOSTREET']);
        $order->setBillingAddress($address);
        $order->setShippingAddress($address);
    }
}
