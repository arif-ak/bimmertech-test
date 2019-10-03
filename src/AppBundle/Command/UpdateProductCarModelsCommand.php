<?php

namespace AppBundle\Command;

use AppBundle\Entity\CarModel;
use AppBundle\Entity\ProductCarModel;
use AppBundle\Entity\ProductVariant;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OrderStateCommand
 * @package AppBundle\Command
 */
class UpdateProductCarModelsCommand extends Command
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        ObjectManager $objectManager,
        ContainerInterface $container
    )
    {
        parent::__construct('app:update-product-car-models');
        $this->objectManager = $objectManager;
        $this->container = $container;
    }

    /**
     * Set order state canceled after one month if order not paid
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->objectManager;

        if (!$productVariants = $em->getRepository(ProductVariant::class)->findAll()) {
            print_r('Product not found');
            exit;
        }

        $carModelRepository = $em->getRepository(CarModel::class);
        $productCarModelRepository = $em->getRepository(ProductCarModel::class);

        $this->deleteProductCarMdodel();

        /** @var ProductVariant $item */
        foreach ($productVariants as $item) {
            $productId = $item->getVincheckserviceId();
            if (!$vinData = $this->container->get('app.vin_check.vin_check')->getVinCheckProductModel($productId)) {
                echo 'Vincheck product id = ' . $productId . ' not found';
                echo "\n";
            } else {
                foreach ($vinData as $value) {
                    if ($model = $carModelRepository->findOneByCode($value)) {
                        echo $value;
                        echo "\n";
                        $productCarModel = new ProductCarModel();
                        $productCarModel->setCarModel($model);
                        $productCarModel->setProduct($item->getProduct());

                        $productCarModelRepository->add($productCarModel);
                    }
                }
            }
        }
        print_r('updated');

    }


    private
    function deleteProductCarMdodel()
    {
        $repository = $this->objectManager->getRepository(ProductCarModel::class);

        foreach ($repository->findAll() as $item) {
            $repository->remove($item);
        }
    }

    protected
    function configure()
    {
        $this->setDescription('Update products car models');
    }
}
