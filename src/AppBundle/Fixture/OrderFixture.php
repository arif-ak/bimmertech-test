<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\Warehouse;
use AppBundle\Repository\WarehouseRepository;
use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Channel\Model\Channel;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Webmozart\Assert\Assert;

class OrderFixture extends AbstractFixture
{

    /**
     * @var FactoryInterface
     */
    private $orderItemFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;

    /**
     * @var ObjectManager
     */
    private $orderManager;

    /**
     * @var RepositoryInterface
     */
    private $channelRepository;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var OrderShippingMethodSelectionRequirementCheckerInterface
     */
    private $orderShippingMethodSelectionRequirementChecker;

    /**
     * @var OrderPaymentMethodSelectionRequirementCheckerInterface
     */
    private $orderPaymentMethodSelectionRequirementChecker;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var WarehouseRepository
     */
    private $warehouseRepository;

    /**
     * @var
     */
    private $channelPricingRepository;

    /**
     * @param FactoryInterface $orderItemFactory
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     * @param ObjectManager $orderManager
     * @param RepositoryInterface $channelRepository
     * @param RepositoryInterface $customerRepository
     * @param RepositoryInterface $productRepository
     * @param RepositoryInterface $countryRepository
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param FactoryInterface $addressFactory
     * @param StateMachineFactoryInterface $stateMachineFactory
     * @param OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
     * @param OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker
     * @param RepositoryInterface $warehouseRepository
     * @param RepositoryInterface $channelPricingRepository
     */
    public function __construct(
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        ObjectManager $orderManager,
        RepositoryInterface $channelRepository,
        RepositoryInterface $customerRepository,
        RepositoryInterface $productRepository,
        RepositoryInterface $countryRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        FactoryInterface $addressFactory,
        StateMachineFactoryInterface $stateMachineFactory,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        RepositoryInterface $warehouseRepository,
        RepositoryInterface $channelPricingRepository
    )
    {
        $this->orderItemFactory = $orderItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderManager = $orderManager;
        $this->channelRepository = $channelRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->countryRepository = $countryRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->addressFactory = $addressFactory;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderShippingMethodSelectionRequirementChecker = $orderShippingMethodSelectionRequirementChecker;
        $this->orderPaymentMethodSelectionRequirementChecker = $orderPaymentMethodSelectionRequirementChecker;
        $this->warehouseRepository = $warehouseRepository;
        $this->channelPricingRepository= $channelPricingRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {

        /** @var Channel $channels */
        $channel = $this->channelRepository->findOneByCode($options['channel_code']);

        /** @var Country $country */
        $country = $this->countryRepository->findOneByCode($options['country_code']);

        /** @var Warehouse $warehouse */
        $warehouse = $this->warehouseRepository->findOneByName($options['warehouse']);

        $customers = $this->customerRepository->findAll();
        $customer = $this->faker->randomElement($customers);

        for ($i = 0; $i < $options['amount']; ++$i) {

            $currencyCode = $channel->getBaseCurrency()->getCode();
            $localeCode = $this->faker->randomElement($channel->getLocales()->toArray())->getCode();

            $order = new Order();
            $order->setLocaleCode($localeCode);
            $order->setChannel($channel);
            $order->setCustomer($customer);
            $order->setCurrencyCode($currencyCode);
            $order->setWarehouse($warehouse);
            $order->setVin($options['vin_number']);
            $order->setCountryCode($country->getCode());
            $order->setCheckoutState('completed');
            $order->setPaymentState('awaiting_payment');
            $order->setShippingState('ready');
            $order->setState('new');
            $order->setUpdatedAt( new \DateTime('now'));
            $order->setNumber(random_int(10, 10000));

            $this->generateItems($order);
            $this->address($order, $country->getCode());
            $this->selectShipping($order);
            $this->selectPayment($order);

            $this->completeCheckout($order);

            $this->orderManager->persist($order);

            if (0 === ($i % 50)) {
                $this->orderManager->flush();
            }
        }
        $this->orderManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
            ->integerNode('amount')->isRequired()->min(0)->end()
            ->scalarNode('country_code')->cannotBeEmpty()->end()
            ->scalarNode('channel_code')->cannotBeEmpty()->end()
            ->scalarNode('warehouse')->cannotBeEmpty()->end()
            ->scalarNode('vin_number')->cannotBeEmpty()->end();
    }

    /**
     * @param OrderInterface $order
     */
    private function generateItems(OrderInterface $order): void
    {
        $numberOfItems = random_int(1, 5);
        $products = $this->productRepository->findAll();

        for ($i = 0; $i < $numberOfItems; ++$i) {

            /** @var OrderItem $item */
            $item = $this->orderItemFactory->createNew();

            /** @var Product $product */
            $product = $this->faker->randomElement($products);

            /** @var ProductVariant $variant */
            $variant = $product->getVariants()->first();

            /** @var ChannelPricing $pricing */
            $pricing = $this->channelPricingRepository->findOneByProductVariant($variant);

            $item->setVariant($variant);
            $item->setWarehouse($variant->getDefaultWarehouse());
            $item->setUnitPrice($pricing->getPrice());

            $this->orderItemQuantityModifier->modify($item, random_int(1, 5));

            $order->addItem($item);
        }
    }

    /**
     * @param OrderInterface $order
     * @param string $countryCode
     */
    private function address(OrderInterface $order, string $countryCode): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setFirstName($this->faker->firstName);
        $address->setLastName($this->faker->lastName);
        $address->setStreet($this->faker->streetName);
        $address->setCountryCode($countryCode);
        $address->setCity($this->faker->city);
        $address->setPostcode($this->faker->postcode);

        $order->setShippingAddress($address);
        $order->setBillingAddress(clone $address);
    }

    /**
     * @param OrderInterface $order
     */
    private function selectShipping(OrderInterface $order): void
    {
        $shippingMethod = $this
            ->faker
            ->randomElement($this->shippingMethodRepository->findEnabledForChannel($order->getChannel()));
        Assert::notNull($shippingMethod, 'Shipping method should not be null.');

        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function selectPayment(OrderInterface $order): void
    {
        $paymentMethod = $this
            ->faker
            ->randomElement($this->paymentMethodRepository->findEnabledForChannel($order->getChannel()));
        Assert::notNull($paymentMethod, 'Payment method should not be null.');

        foreach ($order->getPayments() as $payment) {
            $payment->setMethod($paymentMethod);
        }
    }

    /**
     * @param OrderInterface $order
     */
    private function completeCheckout(OrderInterface $order): void
    {
        if ($this->faker->boolean(25)) {
            $order->setNotes($this->faker->sentence);
        }
    }
}