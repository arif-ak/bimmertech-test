<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\CarModel;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class CarModelFixture
 * @package AppBundle\Fixture
 */
class CarModelFixture extends AbstractFixture implements FixtureInterface
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
        foreach ($this->getData() as $item) {
            $carModel = new CarModel();
            $carModel->setTitle($item['title']);
            $carModel->setCode($item['code']);
            $carModel->setLabel($item['label']);
            $carModel->setYear($item['year']);
            $carModel->setModel($item['model']);
            $carModel->setSeries($item['serie']);

            $this->objectManager->persist($carModel);
        }
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'car_model';
    }

    private function getData()
    {
        return [
            ['title' => '1 Series Convertible E88 (2006-2013)', 'code' => 'e88', 'label' => 'BMW \'06 - \'13 1-Series', 'year' => '2010', 'model' => 'Convertible E88', 'serie' => '1 series'],
            ['title' => '1 Series Coupé E82 (2006-2013)', 'code' => 'e82', 'label' => 'BMW \'06 - \'13 1-Series', 'year' => '2010', 'model' => 'Coupé E82', 'serie' => '1 series'],
            ['title' => '1 Series 3-door Hatchback E81 (2006-2011)', 'code' => 'e81', 'label' => 'BMW \'06 - \'11 1-Series', 'year' => '2010', 'model' => 'Hatchback E81', 'serie' => '1 series'],
            ['title' => '1 Series 5-door Hatchback E87 (2006-2011)', 'code' => 'e87', 'label' => 'BMW \'06 - \'11 1-Series', 'year' => '2010', 'model' => 'Hatchback E87', 'serie' => '1 series'],
            ['title' => '1 Series 5-door Hatchback F20 (2010-2018)', 'code' => 'f20', 'label' => 'BMW \'10 - \'18 1 - Series', 'year' => '2010', 'model' => 'Hatchback F20', 'serie' => '1 series'],
            ['title' => '1 Series 3 - door Hatchback F21(2010 - 2018)', 'code' => 'f21', 'label' => 'BMW \'10 - \'18 1-Series', 'year' => '2010', 'model' => 'Hatchback F21', 'serie' => '1 series'],
            ['title' => '1 Series Sedan F52 (2015-2018)', 'code' => 'f52', 'label' => 'BMW \'15 - \'18 1-Series', 'year' => '2015', 'model' => 'Hatchback F52', 'serie' => '1 series'],
            ['title' => '2 Series Active tourer F45 (2013-2018)', 'code' => 'f45', 'label' => 'BMW \'13 - \'18 2-Series', 'year' => '2015', 'model' => 'Active tourer F45', 'serie' => '2 series'],
            ['title' => '2 Series Convertible F23 (2014-2018)', 'code' => 'f23', 'label' => 'BMW \'14 - \'18 2-Series', 'year' => '2015', 'model' => 'Convertible F23', 'serie' => '2 series'],
            ['title' => '2 Series Coupe F22 (2012-2018)', 'code' => 'f22', 'label' => 'BMW \'12 - \'18 2-Series', 'year' => '2015', 'model' => 'Coupe F22', 'serie' => '2 series'],
            ['title' => '2 Series Coupe M2 F87 (2014-2018)', 'code' => 'f87', 'label' => 'BMW \'14 - \'18 2-Series', 'year' => '2015', 'model' => 'Coupe M2 F87', 'serie' => '2 series'],
            ['title' => '2 Series Grand Tourer F46 (2014-2018)', 'code' => 'f46', 'label' => 'BMW \'14 - \'18 2-Series', 'year' => '2015', 'model' => 'Grand Tourer F46', 'serie' => '2 series'],
            ['title' => '3 Series Coupe E92 (2005-2013)', 'code' => 'e92', 'label' => 'BMW \'05 - \'13 3 - Series', 'year' => '2010', 'model' => 'Coupe E92', 'serie' => '3 series'],
            ['title' => '3 Series Convertible E93(2005 - 2013)', 'code' => 'e93', 'label' => 'BMW \'05 - \'13 3 - Series', 'year' => '2010', 'model' => 'Convertible E93', 'serie' => '3 series'],
            ['title' => '3 Series Gran Turismo F34(2012 - 2018)', 'code' => 'f34', 'label' => 'BMW \'12 - \'18 3 - Series', 'year' => '2015', 'model' => 'Gran Turismo F34', 'serie' => '3 series'],
            ['title' => '3 Series Sedan E90(2004 - 2012)', 'code' => 'e90', 'label' => 'BMW \'04 - \'12 3 - Series', 'year' => '2010', 'model' => 'Sedan E90', 'serie' => '3 series'],
            ['title' => '3 Series Sedan F30(2011 - 2018)', 'code' => 'f30', 'label' => 'BMW \'11 - \'18 3 - Series', 'year' => '2015', 'model' => 'Sedan F30', 'serie' => '3 series'],
            ['title' => '3 Series Sedan F35(2011 - 2018)', 'code' => 'f35', 'label' => 'BMW \'11 - \'18 3 - Series', 'year' => '2015', 'model' => 'Sedan F35', 'serie' => '3 series'],
            ['title' => '3 Series Sedan M3 F80(2012 - 2018)', 'code' => 'f80', 'label' => 'BMW \'12 - \'18 3 - Series', 'year' => '2015', 'model' => 'Sedan M3 F80', 'serie' => '3 series'],
            ['title' => '3 Series Touring E91(2004 - 2012)', 'code' => 'e91', 'label' => 'BMW \'04 - \'12 3 - Series', 'year' => '2010', 'model' => 'Touring E91', 'serie' => '3 series'],
            ['title' => '3 Series Touring F31(2011 - 2018)', 'code' => 'f31', 'label' => 'BMW \'11 - \'18 3 - Series', 'year' => '2015', 'model' => 'Touring F31', 'serie' => '3 series'],
            ['title' => '4 Series Convertible F33(2013 - 2017)', 'code' => 'f33', 'label' => 'BMW \'13 - \'17 4-Series', 'year' => '2015', 'model' => 'Convertible F33', 'serie' => '4 series'],
            ['title' => '4 Series Coupé F32 (2012-2018)', 'code' => 'f32', 'label' => 'BMW \'12 - \'18 4-Series', 'year' => '2015', 'model' => 'Coupé F32', 'serie' => '4 series'],
            ['title' => '4 Series Gran Coupé F36 (2013-2018)', 'code' => 'f36', 'label' => 'BMW \'13 - \'18 4-Series', 'year' => '2015', 'model' => 'Gran Coupé F36', 'serie' => '4 series'],
            ['title' => '4 Series M4 Convertible F83 (2013-2018)', 'code' => 'f83', 'label' => 'BMW \'13 - \'18 4-Series', 'year' => '2015', 'model' => 'M4 Convertible F83', 'serie' => '4 series'],
            ['title' => '4 Series M4 Coupé F82 (2013-2018)', 'code' => 'f82', 'label' => 'BMW \'13 - \'18 4-Series', 'year' => '2015', 'model' => 'M4 Coupé F82', 'serie' => '4 series'],
            ['title' => '5 Series Gran Turismo F07 (2008-2017)', 'code' => 'f07', 'label' => 'BMW \'08 - \'17 5-Series', 'year' => '2015', 'model' => 'Gran Turismo F07', 'serie' => '5 series'],
            ['title' => '5 Series Sedan E60 (2001-2010)', 'code' => 'e60', 'label' => 'BMW \'01 - \'10 5-Series', 'year' => '2010', 'model' => 'Sedan E60', 'serie' => '5 series'],
            ['title' => '5 Series Sedan F10 (2009-2016)', 'code' => 'f10', 'label' => 'BMW \'09 - \'16 5-Series', 'year' => '2015', 'model' => 'Sedan F10', 'serie' => '5 series'],
            ['title' => '5 Series Sedan G30 (2015-2018)', 'code' => 'g30', 'label' => 'BMW \'15 - \'18 5-Series', 'year' => '2015', 'model' => 'Sedan G30', 'serie' => '5 series'],
            ['title' => '5 Series Sedan M5 F90 (2016-2018)', 'code' => 'm5', 'label' => 'BMW \'16 - \'18 5-Series', 'year' => '2016', 'model' => 'Sedan M5 F90', 'serie' => '5 series'],
            ['title' => '5 Series Touring G31 (2016-2018)', 'code' => 'g31', 'label' => 'BMW \'16 - \'18 5-Series', 'year' => '2016', 'model' => 'Touring G31', 'serie' => '5 series'],
            ['title' => '5 Series Wagon E61 (2002-2010)', 'code' => 'e61', 'label' => 'BMW \'02 - \'10 5-Series', 'year' => '2010', 'model' => 'Wagon E61', 'serie' => '5 series'],
            ['title' => '5 Series Wagon F11 (2009-2017)', 'code' => 'f11', 'label' => 'BMW \'09 - \'17 5-Series', 'year' => '2015', 'model' => 'Wagon F11', 'serie' => '5 series'],
            ['title' => '6 Series Convertible E64 (2002-2010)', 'code' => 'e64', 'label' => 'BMW \'02 - \'10 6-Series', 'year' => '2010', 'model' => 'Convertible E64', 'serie' => '6 series'],
            ['title' => '6 Series Convertible F12 (2009-2018)', 'code' => 'f12', 'label' => 'BMW \'09 - \'18 6-Series', 'year' => '2015', 'model' => 'Convertible F12', 'serie' => '6 series'],
            ['title' => '6 Series Coupé E63 (2002-2010)', 'code' => 'e63', 'label' => 'BMW \'02 - \'10 6-Series', 'year' => '2010', 'model' => 'Coupé E63', 'serie' => '6 series'],
            ['title' => '6 Series Coupé F13 (2010-2015)', 'code' => 'f13', 'label' => 'BMW \'10 - \'15 6-Series', 'year' => '2015', 'model' => 'Coupé F13', 'serie' => '6 series'],
            ['title' => '6 Series Gran Coupé F06 (2011-2018)', 'code' => 'f06', 'label' => 'BMW \'11 - \'18 6-Series', 'year' => '2015', 'model' => 'Gran Coupé F06', 'serie' => '6 series'],
            ['title' => '6 Series Gran Turismo G32 (2016-2018)', 'code' => 'g32', 'label' => 'BMW \'16 - \'18 6-Series', 'year' => '2016', 'model' => 'Gran Turismo G32', 'serie' => '6 series'],
            ['title' => '7 Series Hybrid F04 (2008-2012)', 'code' => 'f04', 'label' => 'BMW \'08 - \'12 7-Series', 'year' => '2010', 'model' => 'Series Hybrid F04', 'serie' => '7 series'],
            ['title' => '7 Series Long Sedan E66 (2000-2008)', 'code' => 'e66', 'label' => 'BMW \'00 - \'08 7-Series', 'year' => '2008', 'model' => 'Long Sedan E66', 'serie' => '7 series'],
            ['title' => '7 Series Long Sedan E67 (2002-2008)', 'code' => 'e67', 'label' => 'BMW \'02 - \'08 7-Series', 'year' => '2008', 'model' => 'Long Sedan E67', 'serie' => '7 series'],
            ['title' => '7 Series Long Sedan F02 (2007-2015)', 'code' => 'f02', 'label' => 'BMW \'07 - \'15 7-Series', 'year' => '2015', 'model' => 'Long Sedan F02', 'serie' => '7 series'],
            ['title' => '7 Series Long Sedan F03 (2008-2012)', 'code' => 'f03', 'label' => 'BMW \'08 - \'12 7-Series', 'year' => '2010', 'model' => 'Long Sedan F03', 'serie' => '7 series'],
            ['title' => '7 Series Long Sedan G12 (2014-2018)', 'code' => 'g12', 'label' => 'BMW \'14 - \'18 7-Series ', 'year' => '2015', 'model' => 'Long Sedan G12', 'serie' => '7 series'],
            ['title' => '7 Series Sedan E65 (2000-2008)', 'code' => 'e65', 'label' => 'BMW \'00 - \'08 7-Series', 'year' => '2008', 'model' => 'Sedan E65', 'serie' => '7 series'],
            ['title' => '7 Series Sedan F01 (2007-2015)', 'code' => 'f01', 'label' => 'BMW \'07 - \'15 7-Series', 'year' => '2015', 'model' => 'Sedan F01', 'serie' => '7 series'],
            ['title' => '7 Series Sedan G11 (2014-2018)', 'code' => 'g11', 'label' => 'BMW \'14 - \'18 7-Series', 'year' => '2015', 'model' => 'Sedan G12', 'serie' => '7 series'],
            ['title' => 'I3 (I01) (2013-2018)', 'code' => 'i01', 'label' => 'BMW \'12 - \'18 I3', 'year' => '2015', 'model' => 'I3 (I01)', 'serie' => 'I3 series'],
            ['title' => 'X1 Series Crossover E84 (2008-2015)', 'code' => 'e84', 'label' => 'BMW \'08 - \'15 X1', 'year' => '2015', 'model' => 'Crossover E84', 'serie' => 'X1'],
            ['title' => 'X1 Series Crossover F48 (2014-2018)', 'code' => 'f48', 'label' => 'BMW \'14 - \'18 X1', 'year' => '2015', 'model' => 'Crossover F48', 'serie' => 'X1'],
            ['title' => 'X1 Series Crossover F49 (2014-2018)', 'code' => 'f49', 'label' => 'BMW \'14 - \'18 X1', 'year' => '2015', 'model' => 'Crossover F49', 'serie' => 'X1'],
            ['title' => 'X2 Series SUV F39 (2016-2018)', 'code' => 'f39', 'label' => 'BMW \'16 - \'18 X2', 'year' => '2016', 'model' => 'SUV F39', 'serie' => 'X2'],
            ['title' => 'X3 Series SUV E83 (2003-2010)', 'code' => 'e83', 'label' => 'BMW \'03 - \'10 X3', 'year' => '2010', 'model' => 'SUV E83', 'serie' => 'X3'],
            ['title' => 'X3 Series SUV F25 (2009-2017)', 'code' => 'f25', 'label' => 'BMW \'09 - \'17 X3', 'year' => '2015', 'model' => 'SUV F25', 'serie' => 'X3'],
            ['title' => 'X3 Series SUV G01 (2016-2018)', 'code' => 'g01', 'label' => 'BMW \'16 - \'18 X3', 'year' => '2016', 'model' => 'SUV G01', 'serie' => 'X3'],
            ['title' => 'X4 Series Crossover F26 (2013-2018)', 'code' => 'f26', 'label' => 'BMW \'13 - \'18 X4', 'year' => '2015', 'model' => 'Crossover F26', 'serie' => 'X4'],
            ['title' => 'X5 Series SUV E70 (2006-2013)', 'code' => 'e70', 'label' => 'BMW \'06 - \'13 X5', 'year' => '2010', 'model' => 'SUV E70', 'serie' => 'X5'],
            ['title' => 'X5 Series SUV F15 (2012-2018)', 'code' => 'f15', 'label' => 'BMW \'12 - \'18 X5', 'year' => '2015', 'model' => 'SUV F15', 'serie' => 'X5'],
            ['title' => 'X5 Series SUV F85 (2013-2018)', 'code' => 'f85', 'label' => 'BMW \'13 - \'18 X5', 'year' => '2015', 'model' => 'SUV F85', 'serie' => 'X5'],
            ['title' => 'X5 Series SUV G05 (2017-2019)', 'code' => 'f85', 'label' => 'BMW \'17 - \'19 X5', 'year' => '2018', 'model' => 'SUV G05', 'serie' => 'X5'],
            ['title' => 'X6 Series Crossover E71 (2007-2014)', 'code' => 'e71', 'label' => 'BMW \'07 - \'14 X6', 'year' => '2010', 'model' => 'Crossover E71', 'serie' => 'X6'],
            ['title' => 'X6 Series Crossover F16 (2013-2018)', 'code' => 'f16', 'label' => 'BMW \'13 - \'18 X6', 'year' => '2015', 'model' => 'Crossover F16', 'serie' => 'X6'],
            ['title' => 'X6 Series Crossover M F86 (2013-2018)', 'code' => 'f86', 'label' => 'BMW \'13 - \'18 X6', 'year' => '2015', 'model' => 'Crossover M F86', 'serie' => 'X6'],
            ['title' => 'X6 Series Hybrid E72 (2008-2011)', 'code' => 'e72', 'label' => 'BMW \'08 - \'11 X6', 'year' => '2010', 'model' => 'Hybrid E72', 'serie' => 'X6'],
            ['title' => 'Z4 Series Roadster E89 (2008-2016)', 'code' => 'e89', 'label' => 'BMW \'08 - \'16 Z4', 'year' => '2015', 'model' => ' Roadster E89', 'serie' => 'Z4'],
            ['title' => 'Mini Clubman F54', 'code' => 'f54', 'label' => 'MINI F54', 'year' => '2015', 'model' => 'Clubman F54', 'serie' => 'Mini'],
            ['title' => 'Mini F55', 'code' => 'f55', 'label' => 'MINI F55', 'year' => '2015', 'model' => 'F55', 'serie' => 'Mini'],
            ['title' => 'Mini F56', 'code' => 'f56', 'label' => 'MINI F56', 'year' => '2015', 'model' => 'F56', 'serie' => 'Mini'],
            ['title' => 'Mini Convertible F57', 'code' => 'f57', 'label' => 'MINI F57', 'year' => '2018', 'model' => 'Convertible F57', 'serie' => 'Mini'],
            ['title' => 'Mini Countryman F60', 'code' => 'f60', 'label' => 'MINI F60', 'year' => '2017', 'model' => 'Countryman F60', 'serie' => 'Mini'],
            ['title' => 'Mini Paceman R60', 'code' => 'r60', 'label' => 'MINI R60', 'year' => '2017', 'model' => 'Paceman R60', 'serie' => 'Mini'],
            ['title' => 'Mini Paceman R61', 'code' => 'r61', 'label' => 'MINI R61', 'year' => '2017', 'model' => 'Paceman R61', 'serie' => 'Mini'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->scalarNode('load')->cannotBeEmpty()->end();
    }
}
