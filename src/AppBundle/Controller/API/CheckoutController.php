<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderInterface;
use AppBundle\Entity\PaymentMethod;
use AppBundle\Entity\UserSale;
use AppBundle\Entity\Warehouse;
use AppBundle\Repository\ChannelRepository;
use AppBundle\Repository\OrderRepository;
use DateInterval;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\JobQueueBundle\Entity\Job;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Channel\Model\Channel;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Intl\Intl;

/**
 * Class CheckoutController
 *
 * @package AppBundle\Controller\API
 */
class CheckoutController extends Controller
{
    /**
     * @var EntityRepository
     */
    private $countryRepository;

    /**
     * @var EntityRepository
     */
    private $salesUserRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CustomerRepository;
     */
    private $customerRepository;

    /**
     * @var ChannelRepository
     */
    private $channelRepository;

    /**
     * CheckoutController constructor.
     * @param EntityRepository $countryRepository
     * @param EntityRepository $salesUserRepository
     * @param OrderRepository $orderRepository
     * @param ObjectManager $objectManager
     * @param CustomerRepository $customerRepository
     * @param ChannelRepository $channelRepository

     */
    public function __construct(
        EntityRepository $countryRepository,
        EntityRepository $salesUserRepository,
        OrderRepository $orderRepository,
        ObjectManager $objectManager,
        CustomerRepository $customerRepository,
        ChannelRepository $channelRepository
    )
    {
        $this->countryRepository = $countryRepository;
        $this->salesUserRepository = $salesUserRepository;
        $this->orderRepository = $orderRepository;
        $this->objectManager = $objectManager;
        $this->customerRepository = $customerRepository;
        $this->channelRepository = $channelRepository;
    }

    /**
     * Get thank you page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getThankyou(Request $request)
    {
        return $this->render('@SyliusShop/Order/thankYou.html.twig');
    }

    /**
     *  Create payment
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function setOrderPay(Request $request): JsonResponse
    {
        $cartId = $request->getSession()->get('_payment_cart') ?: 0;

        if (!$cartId) {
            return new JsonResponse('Cart not found', 404);
        }
        /** @var Order $order */

        if (!$order = $this->orderRepository->find($cartId)) {
            return new JsonResponse('Cart not found', 404);
        }


        $this->selectPayment($order);

        $order->setCheckoutCompletedAt(new \DateTime('now'));
        $order->setCheckoutState(OrderInterface::STATUS_COMPLETE);

        $hash = substr(md5(openssl_random_pseudo_bytes(20)), -20);
        $order->setTokenValue($hash);
        $order->setState(OrderInterface::STATUS_NEW);

        $this->orderRepository->add($order);

        $data = [
            'url' => '/order/' . $order->getTokenValue() . '/pay',
            'orderId' => $order->getId()
        ];

        return new JsonResponse($data);
    }

    /**
     * Set order detail
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setOrderDetail(Request $request): JsonResponse
    {

        $country = $request->request->get('country');
        $vin = $request->request->get('vin');
        $email = $request->request->get('email');
        $fistName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $phone = $request->request->get('phone');
        /** @var Channel $mainChannel */
        $mainChannel = $this->channelRepository->findOneByHostname($request->getHost());
        $cartId = $request->getSession()->get('_sylius.cart.' . $mainChannel->getCode());

        if (!$cartId) {
            return new JsonResponse('Cart not found', 404);
        }
        /** @var Order $order */
        $order = $this->orderRepository->find($cartId);

        if (!$country || !$vin || !$email || !$fistName || !$lastName || !$phone) {
            return new  JsonResponse('Bad parameter', 400);
        }

        if (!$this->addCustomer($request, $order, $fistName, $lastName, $phone, $vin)) {
            return new  JsonResponse('User not found', 404);
        }

        if (!$warehouse = $this->getWarehouse()) {
            return new  JsonResponse('Warehouse don\' has payment method', 400);
        }

        $order->setWarehouse($warehouse);
        $this->addSalesUser($request, $order);
        $order->setCountryCode($country);
        $order->setVin($vin);
        $order->setNotes($request->request->get('comments'));
        $order->setState(OrderInterface::STATE_CART);
        $order->setCheckoutState('addressed');

        $order = $this->get('app.service.order_status')->update($order);

        $this->objectManager->persist($order);
        $this->objectManager->flush();

        $session = (new Session())->set('_payment_cart', $cartId);

        return new JsonResponse('Order details saved');
    }

    /**
     * Get sales user
     *
     * @return JsonResponse
     */
    public function getSalesUsers(): JsonResponse
    {
        if (!$users = $this->salesUserRepository->findAll()) {
            return new JsonResponse('Users not found', 404);
        }
        $array = [];
        foreach ($users as $user) {
            /** @var UserSale $user */
            $array[] = [
                'id' => $user->getId(),
                'name' => $user->getFirstName() . ' ' . $user->getLastName()
            ];
        }
        sort($array);

        return new JsonResponse($array);
    }

    /**
     * Get shop country
     *
     * @return JsonResponse
     */
    public function getCountries(): JsonResponse
    {
        if (!$countries = $this->countryRepository->findAll()) {
            return new JsonResponse('Country not found', 404);
        }
        $array = [];
        /** @var Country $country */
        foreach ($countries as $country) {
            $array[] = [
                'code' => $country->getCode(),
                'name' => Intl::getRegionBundle()->getCountryName($country->getCode())
            ];
        }
        sort($array);

        return new JsonResponse($array);
    }

    /**
     * Get sales user by id
     *
     * @param Request $request
     * @param Order $order
     * @return void
     */
    private function addSalesUser(Request $request, Order $order): void
    {
        $id = $request->request->get('salesRep') ?: 0;

        if ($SalesUser = $this->salesUserRepository->find($id)) {
            $order->setUserSale($SalesUser);
        }
    }

    /**
     * Get customer by email
     *
     * @param Request $request
     * @param Order $order
     * @param $fistName
     * @param $lastName
     * @param $phone
     * @return Customer
     */
    private function addCustomer(Request $request, Order $order, $fistName, $lastName, $phone, $vinNumber): ?Customer
    {
        $email = $request->request->get('email');

        /** @var Customer $customer */
        if ($customer = $this->customerRepository->findOneByEmail($email)) {
            $order->setCustomer($customer);
            $order->setCustomerIp($request->server->get('REMOTE_ADDR'));

            $customer->getFirstName() ?: $customer->setFirstName($fistName);
            $customer->getLastName() ?: $customer->setLastName($lastName);
            $customer->getPhoneNumber() ?: $customer->setPhoneNumber($phone);
            $customer->getVinNumber() ?: $customer->setVinNumber($vinNumber);

            return $customer;
        }
    }

    /**
     * Selected payment method
     *
     * @param Order $order
     * @return Order
     */
    private function selectPayment(Order $order): Order
    {
        /** @var PaymentInterface $payment */
        $payment = $this->get('sylius.factory.payment')->createNew();
        /** @var PaymentMethod $paymentMethod */
        $orderNumberAssigner = $this->get('sylius.order_number_assigner');

        $orderNumberAssigner->assignNumber($order);

        $paymentMethod = $order->getWarehouse()->getPaymentMethod()->first();
        $payment->setMethod($paymentMethod);
        $payment->setCurrencyCode($order->getCurrencyCode());
        $payment->setState('new');
        $payment->setAmount($order->getTotal());
        $order->addPayment($payment);
        $order->setPaymentState('awaiting_payment');

        return $order;
    }

    /**
     * @param $email
     * @param $data
     * @param $type
     */
    protected function sendMail($email, $data, $type): void
    {
        $helper = $this->get('app.service.create_shop_user');
        $helper->sendMail($email, $data, $type);
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse()
    {
        $warehouses = $this->get('app.repository.warehouse')->findAll();
        /** @var Warehouse $item */
        foreach ($warehouses as $item) {
            if ($item->getPaymentMethod()->count() > 0) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function closePayment(Request $request): JsonResponse
    {
        if (!$email = $request->get('email')) {
            return new JsonResponse('Bad parameter email', JsonResponse::HTTP_BAD_REQUEST);
        }

        $orderId = $request->get('orderId');
        /** @var Order $order */
        $order = $this->orderRepository->find($orderId);
        $arguments = [
            str_replace(' ', '', $email),
            'shop_user_payment_close',
            $order->getCustomer()->getFullName()
        ];

        $roles = ['ROLE_SALE_ACCESS', 'ROLE_ADMINISTRATION_ACCESS'];
        $dataProducts = $this->container->get('app.service.check_item_type')->checkType($order);

        $this->container->get('app.service.admin_user')
            ->sendEmail($roles, 'app_after_order_created', ['order' => $order, 'dataProducts' => $dataProducts]);


        if ($job = $this->get('jms_job_queue.job_manager')->findJob('app:send-email', $arguments)) {
            return new JsonResponse('Email was sand');
        }

        $arguments[1] = 'app_abandonded_cart';
        $arguments[2] = strval($order->getId());


        $job = new Job('app:send-email', $arguments);
        $job->setMaxRetries(5);
        $date = new DateTime('now');
        $date->add(new DateInterval('PT24H'));
        $job->setExecuteAfter($date);
        $this->objectManager->persist($job);
        $this->objectManager->flush();

        return new JsonResponse('created');
    }
}
