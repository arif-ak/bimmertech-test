<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\ShippingMethod;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGateway;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class ShippingGatewayFixture
 * @package AppBundle\Fixture
 */
class ShippingGatewayFixture extends AbstractFixture implements FixtureInterface
{

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Warehouse constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $gateways = $options['gateways'];

        foreach ($gateways as $item) {
            $shippingGateway = new ShippingGateway();
            $shippingGateway->setName($item['name']);
            $shippingGateway->setCode($item['code']);
            $shippingGateway = $this->addShipmentMethod($shippingGateway, $item['shipping_methods']);
            $shippingGateway = $this->addConfig($shippingGateway, $item);

            $this->objectManager->persist($shippingGateway);
        }
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'shipping_gateway';
    }

    /**
     * Add config
     *
     * @param ShippingGateway $shippingGateway
     * @param array $option
     * @return ShippingGateway
     */
    private function addConfig(ShippingGateway $shippingGateway, array $option)
    {
        $config = [
            'gatewayName' => $option['gateway_name'],
            'mode' => $option['mode'],
            'dhlId' => $option['dhl_id'],
            'pass' => $option['password'],
            'shipperAccountNumber' => $option['shipper_account_number'],
            'regionCode' => $option['region_code'],
            'company' => $option['company_name'],
            'countryCode' => $option['country_code'],
            'name' => $option['name'],
            'phone' => $option['phone_number'],
            'postalCode' => $option['postal_code'],
            'city' => $option['city'],
            'address' => $option['address'],
            'bitbagUiLabel' => $option['bitbag_ui_label']
        ];

        $shippingGateway->setConfig($config);

        return $shippingGateway;

    }

    /**
     *  Add shipment method to warehouse
     *
     * @param ShippingGateway $shippingGateway
     * @param array $methods
     * @return ShippingGateway
     */
    private function addShipmentMethod(ShippingGateway $shippingGateway, array $methods)
    {
        foreach ($methods as $item) {

            $shipmentMethod = $this->objectManager->getRepository(ShippingMethod::class)->findOneByCode($item);

            $shippingGateway->addShippingMethod($shipmentMethod);

        }
        return $shippingGateway;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode->children()
            ->arrayNode('gateways')
            ->prototype('array')
            ->children()
            ->scalarNode('code')->cannotBeEmpty()->end()
            ->scalarNode('bitbag_ui_label')->cannotBeEmpty()->end()
            ->arrayNode('shipping_methods')->prototype('scalar')->end()->end()
            ->scalarNode('gateway_name')->cannotBeEmpty()->end()
            ->scalarNode('mode')->cannotBeEmpty()->end()
            ->scalarNode('dhl_id')->cannotBeEmpty()->end()
            ->scalarNode('password')->end()
            ->scalarNode('shipper_account_number')->end()
            ->scalarNode('company_name')->end()
            ->scalarNode('country_code')->end()
            ->scalarNode('name')->cannotBeEmpty()->end()
            ->scalarNode('phone_number')->cannotBeEmpty()->end()
            ->scalarNode('postal_code')->end()
            ->scalarNode('city')->end()
            ->scalarNode('address')->end()
            ->scalarNode('region_code')->end();
    }
}
