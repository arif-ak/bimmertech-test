<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductReview;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class AdminUsersFixture
 *
 * @package AppBundle\Fixture
 */
class ProductReviewFixture extends AbstractFixture
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * ProductReviewFixture constructor.
     * @param ObjectManager $objectManager
     * @param RepositoryInterface $customerRepository
     * @param RepositoryInterface $productRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        RepositoryInterface $customerRepository,
        RepositoryInterface $productRepository
    )
    {
        $this->objectManager = $objectManager;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $products = $this->productRepository->findAll();
        /** @var Product $product */
        $product = $this->faker->randomElement($products);

        $productReviews = $options['custom'];


        foreach ($productReviews as $review) {

            $product->addReview($this->addProductReview($review));
//            $this->addProductReview($review,$product);

            $this->objectManager->persist($product);
        }

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'app_product_review';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->arrayNode('custom')
            ->performNoDeepMerging()
            ->prototype('array')
            ->children()
            ->scalarNode('title')->end()
            ->scalarNode('rating')->end()
            ->scalarNode('comment')->end()
            ->scalarNode('status')->end()
            ->end();
    }

    /**
     * @param array $review
     * @return ProductReview
     */
    public function addProductReview(array $review){
        $customers = $this->customerRepository->findAll();
        $customer = $this->faker->randomElement($customers);

        $productReview = new ProductReview();
        $productReview->setReviewSubject($customer);
        $productReview->setTitle($review['title']);
        $productReview->setRating($review['rating']);
        $productReview->setComment($review['comment']);
        $productReview->setStatus($review['status']);

       // $this->objectManager->persist($productReview);

        return $productReview;
    }
}
