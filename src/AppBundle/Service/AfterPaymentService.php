<?php

namespace AppBundle\Service;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Order;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\JobQueueBundle\Entity\Job;
use JMS\JobQueueBundle\Entity\Repository\JobManager;
use Sylius\Component\Core\Model\Payment;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class AfterPaymentService
 * @package AppBundle\Service
 */
class AfterPaymentService
{
    /**
     * @var CreateShopUser
     */
    private $createShopUserService;

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Container
     */
    private $container;

    /**
     * AfterPaymentService constructor.
     * @param CreateShopUser $createShopUserService
     * @param JobManager $jobManager
     * @param ObjectManager $objectManager
     * @param Container $container
     */
    public function __construct(
        CreateShopUser $createShopUserService,
        JobManager $jobManager,
        ObjectManager $objectManager,
        Container $container
    )
    {
        $this->createShopUserService = $createShopUserService;
        $this->jobManager = $jobManager;
        $this->objectManager = $objectManager;
        $this->container = $container;
    }

    /**
     * @param $object
     * @return Order
     */
    public function setOrderStatus($object)
    {
        /** @var Order $object */
        /** @var Payment $payment */
        if ($payment = $object->getLastPayment()) {
            $object->setPaymentState($payment->getState());
        }
        $email = $object->getCustomer()->getEmail();
        $data = new CheckItemTypeService();
        $dataProduct = $data->checkType($object);
        $this->createShopUserService->sendMail($email, ['order' => $object, 'dataProducts' => $dataProduct, 'host' => $_SERVER['SERVER_NAME']], 'shop_user_create_order');

        // Send email if order was create and pay
        $roles = ['ROLE_SALE_ACCESS', 'ROLE_ADMINISTRATION_ACCESS'];
        $dataProducts = $this->container->get('app.service.check_item_type')->checkType($object);

        $this->container->get('app.service.admin_user')
            ->sendEmail($roles, 'app_after_order_created', ['order' => $object, 'dataProducts' => $dataProducts]);

        return $object;
    }

    /**
     * @param $object
     * @throws \Exception
     */
    public function paymentClosed($object)
    {
        /** @var Customer $customer */
        $customer = $object->getCustomer();
        $email = $customer->getEmail();

        $arguments = [$email, 'shop_user_payment_close', $customer->getFullName() ];
        /** @var Job $job */
        $job = $this->jobManager->getOrCreateIfNotExists('app:send-email', $arguments);
        $job->setMaxRetries(5);
        $this->objectManager->persist($job);
        $this->objectManager->flush();

        $job = $this->jobManager->getOrCreateIfNotExists('app:send-email', $arguments);
        $job->setMaxRetries(5);
        $this->objectManager->persist($job);
        $this->objectManager->flush();
    }

    /**
     * @param Order $order
     * @return array
     */
    public function getOrderData(Order $order)
    {
        /** @var Order $order */
        $products = $order->getItems();
        $arrayProducts = [];
        $total = [];
        foreach ($products as $product) {
            $total[] = $product->getTotal();
            $arrayProducts[] = $product->getVariant()->getProduct();
        }

        $result = [
            'total' => $total,
            'products' => $arrayProducts,
            'number' => $order->getNumber(),
            'order_total' => $order->getTotal(),
            'date' => $order->getCheckoutCompletedAt()->format('y.m.d'),
            'payment_status' => $order->getPaymentState(),
            'shipping_status' => $order->getShippingState(),
        ];

        if ($orderBilling = $order->getBillingAddress()) {
            $address = [
                'first_name' => $orderBilling->getFirstName(),
                'last_name' => $orderBilling->getLastName(),
                'phone' => $orderBilling->getPhoneNumber(),
                'city' => $orderBilling->getCity(),
                'street' => $orderBilling->getStreet(),
                'company' => $orderBilling->getCompany(),
                'country_code' => $orderBilling->getCountryCode(),
                'postcode' => $orderBilling->getPostcode(),
                'province_code' => $orderBilling->getProvinceCode(),
                'province_name' => $orderBilling->getProvinceName(),
            ];

            return array_merge($result, $address);
        }
        return $result;
    }
}