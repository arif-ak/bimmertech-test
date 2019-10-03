<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductDescription;
use AppBundle\Entity\ProductInstaller;
use Sylius\Component\Core\Model\ProductTranslation;
use AppBundle\Entity\ProductVariant;
use AppBundle\Entity\Taxon;
use AppBundle\Entity\Warehouse;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Model\ProductAssociation;
use Sylius\Component\Product\Model\ProductAssociationType;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductFixture extends AbstractFixture implements FixtureInterface
{
    const LOCALE = 'en_US';
    const ASSOCIATIONS = ['Addons', 'IncludedAddons', 'Warranty'];

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @var array
     */
    private $addons = [];

    /**
     * @var array
     */
    private $associations = [];

    /**
     * ProductFixture constructor.
     *
     * @param ObjectManager $em
     * @param ImageUploaderInterface $imageUploader
     */
    public function __construct(ObjectManager $em, ImageUploaderInterface $imageUploader)
    {
        $this->em = $em;
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param array $options
     */
    public function load(array $options): void
    {
        $this->createAssociations();

        $products = $options['custom'];

        foreach ($products as $item) {


            /** @var Product $product */
            $product = $this->em->getRepository(Product::class)->findOneBy(['code' => $item['code']]);

            if (null === $product) {
                $product = new Product();
            }else{
                continue;
            }

            $taxon = $this->getTaxon($item['main_taxon']);

            if (isset($item['taxonDescription'])) {
                $product->setTaxonDescription($item['taxonDescription']);
            }
            if (isset($item['recomended'])) {
                $product->setRecomended($item['recomended']);
            }

            $product->setCode($item['code']);
            $product->setCurrentLocale(self::LOCALE);
            $product->setFallbackLocale(self::LOCALE);
            $product->setEnabled($item['enabled']);
            $product = $this->addChannels($product, $item);
            $product = $this->createVariant($product, $item);
            $product->addTranslation($this->createTranslation($item));
            $product->setMainTaxon($taxon);


            if (isset($item['addon_information'])) {
                $product->setAddonInformation($item['addon_information']);
            }

            if (isset($item['image'])) {
                $product = $this->createImages($product, $item['image']);
            }

            $this->em->persist($product);
            $this->createProductTaxon($product, $taxon);

            if (count($item['descriptions']) > 0) {
                $this->createProductDescription($product, $item['descriptions']);
            }

            if (count($item['installers']) > 0) {
                $this->createProductInstaller($product, $item['installers']);
            }

            if (array_search('addons', $item['channels']) !== false) {
                $this->addons[$item['code']] = $product;
            }
            $this->addProductAssociations($product, $item);
        }

        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'products';
    }

    /**
     * @param ArrayNodeDefinition $optionsNode
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode->children()
            ->arrayNode('custom')
            ->performNoDeepMerging()
            ->prototype('array')
            ->children()
            ->scalarNode('name')->cannotBeEmpty()->end()
            ->scalarNode('code')->cannotBeEmpty()->end()
            ->booleanNode('enabled')->end()
            ->scalarNode('short_description')->cannotBeEmpty()->end()
            ->scalarNode('main_taxon')->cannotBeEmpty()->end()
            ->arrayNode('taxons')->prototype('scalar')->end()->end()
            ->arrayNode('channels')->prototype('scalar')->end()->end()
            ->arrayNode('product_attributes')->prototype('scalar')->end()->end()
            ->arrayNode('product_options')->prototype('scalar')->end()->end()
            ->scalarNode('image')->end()
            ->booleanNode('shipping_required')->end()
            ->scalarNode('price')->end()
            ->scalarNode('original_price')->end()
            ->scalarNode('warehouse')->end()
            ->scalarNode('imageRequired')->end()
            ->scalarNode('instructionRequired')->end()
            ->scalarNode('vincheckserviceId')->end()
            ->scalarNode('amount')->end()
            ->scalarNode('installation_time')->end()
            ->scalarNode('meta_descriptions')->end()
            ->scalarNode('slug')->end()
            ->scalarNode('meta_keywords')->end()
            ->scalarNode('taxonDescription')->end()
            ->scalarNode('addon_information')->end()
            ->booleanNode('recomended')->end()
            ->booleanNode('isContainer')->end()
            ->arrayNode('warranty')->prototype('scalar')->end()->end()
            ->arrayNode('includedAddons')->prototype('scalar')->end()->end()
            ->arrayNode('addons')->prototype('scalar')->end()->end()
            ->arrayNode('descriptions')->prototype('array')
            ->children()
            ->scalarNode('header')->end()
            ->scalarNode('sub_header')->end()
            ->scalarNode('description')->end()
            ->scalarNode('type')->end()
            ->scalarNode('video')->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('installers')->prototype('array')
            ->children()
            ->scalarNode('header')->end()
            ->scalarNode('sub_header')->end()
            ->scalarNode('description')->end()
            ->scalarNode('type')->cannotBeEmpty()->end()
            ->scalarNode('video')->end()
            ->end()
            ->end()
            ->end();
    }


    /**
     * @param Product $product
     * @param $data
     */
    public function createProductDescription(Product $product, $data)
    {
        $concatenate = '';
        foreach ($data as $value) {

            $header = $value['header'];
            $subHeader = $value['sub_header'];
            $description = $value['description'];

            $concatenate .= $concatenate.' '.$header .' ' . $subHeader .' '. $description;

            $product->setDescription($concatenate);

            $this->em->persist($product);
        }
    }


    /**
     * @param Product $product
     * @param $data
     */
    public function createProductInstaller(Product $product, $data)
    {
        $concatenate = '';
        foreach ($data as $value) {
            $header = $value['header'];
            $subHeader = $value['sub_header'];
            $description = $value['description'];

            $concatenate .= $concatenate.' '.$header .' ' . $subHeader .' '. $description;

            $product->setInstaller($concatenate);

            $this->em->persist($product);
        }
    }

    /**
     * @param ProductInstaller $productInstaller
     * @param $image
     * @return ProductInstaller
     */
    private function createImageInstaller(ProductInstaller $productInstaller, $image)
    {
        $uploadedImage = new UploadedFile(__DIR__ . '/../../..' . $image, basename($image));

        $slideImage = $productInstaller->getImages()->first();
        $slideImage->setFile($uploadedImage);
        $this->imageUploader->upload($slideImage);

        return $productInstaller;
    }


    /**
     * @param ProductDescription $productDescription
     * @param $image
     * @return ProductDescription
     */
    private function createImageDescription(ProductDescription $productDescription, $image)
    {
        $uploadedImage = new UploadedFile(__DIR__ . '/../../..' . $image, basename($image));

        $slideImage = $productDescription->getImages()->first();
        $slideImage->setFile($uploadedImage);
        $this->imageUploader->upload($slideImage);

        return $productDescription;
    }

    /**
     * Create product taxon
     *
     * @param Product $product
     * @param Taxon $taxon
     */
    public function createProductTaxon(Product $product, Taxon $taxon)
    {
        $productTaxon = $this->em->getRepository(ProductTaxon::class)->findOneBy(['product' => $product]);

        if (null === $productTaxon) {
            $productTaxon = new ProductTaxon();
        }
       // $productTaxon = new ProductTaxon();
        $productTaxon->setProduct($product);
        $productTaxon->setTaxon($taxon);

        $this->em->persist($productTaxon);
        $this->em->flush();
    }

    /**
     * Get taxon
     *
     * @param string $data
     * @return mixed
     */
    public function getTaxon(string $data)
    {
        return $this->em->getRepository(Taxon::class)->findOneByCode($data);
    }

    /**
     *  Create product variant
     *
     * @param Product $product
     * @param array $options
     * @return Product
     */
    private function createVariant(Product $product, array $options)
    {
        $productVariant = new ProductVariant();

        $productVariant->setCode($options['code']);
        if (!empty($options['warehouse'])) {
            $productVariant->setDefaultWarehouse($this->getWarehouse($options['warehouse']));
        }
        $productVariant->setImageRequired($options['imageRequired']);
        $productVariant->setVincheckserviceId($options['vincheckserviceId']);
        $productVariant->setInstructionRequired($options['instructionRequired']);
        $productVariant->setInstallationTime($options['installation_time']);
        $productVariant->setOnHand($options['amount']);

        foreach ($this->getChannels() as $channel) {
            $channelPricing = $this->createChannelPricing($productVariant, $channel->getCode(), $options);
            $productVariant->addChannelPricing($channelPricing);
            $product->addVariant($productVariant);
        }
        return $product;
    }

    /**
     * Create channel pricing
     *
     * @param $productVariant
     * @param $channelCode
     * @param $data
     * @return ChannelPricing
     */
    private function createChannelPricing($productVariant, $channelCode, $data)
    {
        $chanelPricing = new ChannelPricing();

        $chanelPricing->setChannelCode($channelCode);
        $chanelPricing->setOriginalPrice($data['original_price']);
        $chanelPricing->setPrice($data['price']);
        $chanelPricing->setProductVariant($productVariant);

        return $chanelPricing;
    }

    /**
     * Get Warehouse
     *
     * @param  string $name
     * @return mixed
     */
    private function getWarehouse($name)
    {
        return $this->em->getRepository(Warehouse::class)->findOneByName($name);
    }

    /**
     * Add channels
     *
     * @param Product $product
     * @param array $item
     * @return Product
     */
    private function addChannels(Product $product, array $item)
    {
        foreach ($item['channels'] as $value) {

            $channel = $this->getChannel($value);

            $product->addChannel($channel);
        }
        return $product;
    }

    /**
     * Get channel
     *
     * @param string|null $code
     * @return Channel
     */
    private function getChannel(string $code = null): Channel
    {
        return $this->em->getRepository(Channel::class)->findOneByCode($code);
    }

    /**
     * Get channel
     *
     * @return array|Channel[]
     */
    private function getChannels()
    {
        return $this->em->getRepository(Channel::class)->findAll();
    }

    /**
     * Create translation
     *
     * @param array $data
     * @return ProductTranslation
     */
    private function createTranslation(array $data): ProductTranslation
    {
        $translation = $this->em->getRepository(ProductTranslation::class)->findOneBy(['slug' => mb_strtolower($data['code'])]);

        if (null === $translation) {
            $translation = new ProductTranslation();
        }

        //$translation = new ProductTranslation();
        $translation->setLocale(self::LOCALE);
        $translation->setName($data['name']);
        $translation->setSlug(mb_strtolower($data['code']));
        //$translation->setDescription($data['description']);
        $translation->setShortDescription($data['short_description']);
        $translation->setMetaDescription($data['meta_descriptions']);
        $translation->setMetaKeywords($data['meta_keywords']);

        return $translation;
    }

    /**
     * Create product association
     *
     * @param Product $product
     * @param array $data
     * @return void
     */
    private function addProductAssociations(Product $product, array $data)
    {

        if (count($data['addons'])>0) {
            $this->addOneAssociationType($product, $data, 'addons');
        }

        if (count($data['warranty'])>0) {
            $this->addOneAssociationType($product, $data, 'warranty');
        }

        if (count($data['includedAddons'])>0) {
            $this->addOneAssociationType($product, $data, 'includedAddons');
        }
    }

    /**
     * Add products one type association to product
     *
     * @param Product $product
     * @param array $data
     * @param $type
     */
    private function addOneAssociationType(Product $product, array $data, $type)
    {
        $productAssociation = new  ProductAssociation();
        $productAssociation->setType($this->associations[ucfirst($type)]);
        $productAssociation->setOwner($product);

        foreach ($data[$type] as $addon) {

            try{
                if ($this->addons[$addon]){
                    $associationProduct = $this->addons[$addon];
                    $productAssociation->addAssociatedProduct($associationProduct);

                    $this->em->persist($productAssociation);
                }
            } catch (\Exception $e){
                $message = $e->getMessage();
            }

        }

        $this->em->flush();
    }

    /**
     * Create associations
     */
    private function createAssociations()
    {
        $associations = [];
        foreach (self::ASSOCIATIONS as $name) {

            $productAssociationType = $this->em->getRepository(ProductAssociationType::class)->findOneBy(['code' => $name]);

            if (null === $productAssociationType) {
                $productAssociationType = new ProductAssociationType();
            }

            $productAssociationType->setCurrentLocale(self::LOCALE);
            $productAssociationType->setFallbackLocale(self::LOCALE);
            $productAssociationType->setName($name);
            $productAssociationType->setCode($name);

            $this->em->persist($productAssociationType);
            $this->em->flush();

            $associations[$name] = $productAssociationType;
        }
        $this->associations = $associations;
    }

    /**
     * Create Product image
     *
     * @param Product $product
     * @param $image
     * @return Product
     */
    private function createImages(Product $product, $image)
    {
        $file = new UploadedFile(__DIR__ . '/../../..' . $image, basename($image));

        $productImage = new ProductImage();
        $productImage->setFile($file);
        $product->addImage($productImage);
        $this->imageUploader->upload($productImage);

        return $product;
    }
}
