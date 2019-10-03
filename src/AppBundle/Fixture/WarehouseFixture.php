<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\PaymentMethod;
use AppBundle\Entity\ShippingMethod;
use AppBundle\Entity\TaxCategory;
use AppBundle\Entity\Warehouse;
use AppBundle\Entity\Zone;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class WarehouseFixture
 *
 * @package AppBundle\Fixture
 */
class WarehouseFixture extends AbstractFixture implements FixtureInterface
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
     * @{@inheritdoc}
     */
    public function load(array $options): void
    {
        $warehouses = $options['warehouses'];

        foreach ($warehouses as $item) {
            $warehouse = new Warehouse();
            $warehouse->setName($item['name']);
            $warehouse->setCountry($item['country']);
            $warehouse->setCity($item['city']);
            $warehouse->setZip($item['zip']);
            $warehouse->setAddress($item['address']);
            $warehouse->setPhone($item['phone']);

            $warehouse = $this->addZone($warehouse, $item['zone']);
            $warehouse = $this->addTaxCategory($warehouse, $item['tax_category']);
            $warehouse = $this->addShipmentMethod($warehouse, $item['shipping_method']);
            $warehouse = $this->addPaymentMethod($warehouse, $item['payment_method']);

            $this->objectManager->persist($warehouse);
        }
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'warehouse';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->arrayNode('warehouses')
            ->prototype('array')
            ->children()
            ->scalarNode('name')->cannotBeEmpty()->end()
            ->scalarNode('country')->cannotBeEmpty()->end()
            ->scalarNode('city')->cannotBeEmpty()->end()
            ->scalarNode('zip')->cannotBeEmpty()->end()
            ->scalarNode('address')->cannotBeEmpty()->end()
            ->scalarNode('phone')->cannotBeEmpty()->end()
            ->scalarNode('zone')->end()
            ->scalarNode('tax_category')->end()
            ->arrayNode('payment_method')->prototype('scalar')->end()->end()
            ->arrayNode('shipping_method')->prototype('scalar')->end()->end();
    }

    /**
     * Add Zone to warehouse
     *
     * @param Warehouse $warehouse
     * @param $data
     * @return Warehouse
     */
    private function addZone(Warehouse $warehouse, $data)
    {
        if ($zone = $this->objectManager->getRepository(Zone::class)->findByCode($data)) {
            $warehouse->setZone($zone[0]);
        }
        return $warehouse;
    }

    /**
     * Add tax category  to warehouse
     *
     * @param Warehouse $warehouse
     * @param $data
     * @return Warehouse
     */
    private function addTaxCategory(Warehouse $warehouse, $data)
    {
        if ($taxCategory = $this->objectManager->getRepository(TaxCategory::class)->findByCode($data)) {
            $warehouse->addTaxCategory($taxCategory[0]);
        }
        return $warehouse;
    }

    /**
     *  Add shipment method to warehouse
     *
     * @param Warehouse $warehouse
     * @param array $options
     * @return Warehouse
     */
    private function addShipmentMethod(Warehouse $warehouse, array $options)
    {
        foreach ($options as $shipment) {

            $shipment = $this->objectManager->getRepository(ShippingMethod::class)->findByCode($shipment);
            $warehouse->addShippingMethod($shipment[0]);
        }
        return $warehouse;
    }

    /**
     *  Add payment method to warehouse
     *
     * @param Warehouse $warehouse
     * @param array $options
     * @return Warehouse
     */
    private function addPaymentMethod(Warehouse $warehouse, array $options)
    {
        foreach ($options as $payment) {
            $payment = $this->objectManager->getRepository(PaymentMethod::class)->findByCode($payment);
            $warehouse->addPaymentMethod($payment[0]);
        }
        return $warehouse;
    }
}
