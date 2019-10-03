<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use AppBundle\Serializer\Normalizer\AddonsOptionNormalizer;
use AppBundle\Serializer\Normalizer\BuyersGuideNormalizer;
use AppBundle\Serializer\Normalizer\DropDownsNormalizer;
use AppBundle\Serializer\Normalizer\InterestedInNormalizer;
use AppBundle\Serializer\Normalizer\PopupOptionNormalizer;
use AppBundle\Serializer\Normalizer\ProductAttributesNormalizer;
use AppBundle\Serializer\Normalizer\ProductDescriptionsNormalizer;
use AppBundle\Serializer\Normalizer\ProductNormalizer;
use AppBundle\Serializer\Normalizer\ProductPropertiesNormalizer;
use AppBundle\Serializer\Normalizer\ProductVariantsNormalizer;
use AppBundle\Serializer\Normalizer\SavePriceNormalizer;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Product\Model\ProductAssociation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ProductController
 *
 * @package AppBundle\Controller\API
 */
class ProductController extends Controller
{

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductNormalizer
     */
    private $normalizer;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var
     */
    private $addonsChannel;

    /**
     * ProductController constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get product by id
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function index(Request $request, $id)
    {
        $this->channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $this->addonsChannel = $this->get('sylius.repository.channel')->findOneByCode('addons');
        /** @var Product $product */
        if (!$product = $this->productRepository->find($id)) {
            return new JsonResponse('Product not found', 404);
        }

        $session = new Session();
        $sessionProducts = $session->get('vincheck');

        if ($sessionProducts !== null) {
            $this->get('app.vin_check.compatibility_products')
                ->setOneCompatibility($product, $sessionProducts['products'], $sessionProducts['compatibility']);
            $this->get('app.vin_check.compatibility_products')
                ->popupOptionCompatibility($product, $sessionProducts['popupOption']);
        }

        $this->normalizer = new ProductNormalizer();

        $result = $this->normalizer->normalize($product, 'json', ['channel' => $this->channel]);
        $result['warranty'] = $this->getAddons($product->getAssociations(), 'Warranty');
        $result['addons'] = $this->getAddons($product->getAssociations(), 'Addons');
        $result['includedAddons'] = $this->getAddons($product->getAssociations(), 'IncludedAddons');
        $result['properties'] = (new ProductPropertiesNormalizer())->normalize($product->getProperties());
        $result['specification'] = (new ProductAttributesNormalizer())->normalize($product->getAttributes());
        $result['popup_option'] = (new PopupOptionNormalizer())->normalize($product);
        $result['options'] = $product->getVariants()->count() > 1 ? (new ProductVariantsNormalizer())
            ->normalize($product->getVariants(), 'json', ['channel' => $this->channel]) : [];
        $result['buyers_guide']['products'] = $product->getBuyersRelated()->count() >= 1 ? (new BuyersGuideNormalizer())
            ->normalize($product) : [];
        $result['savePrice'] = (new SavePriceNormalizer())->normalize($product);
        $result['dropDown'] = (new DropDownsNormalizer())->normalize($product);
        $result['productDescriptions'] = (new ProductDescriptionsNormalizer())->normalize($product);

        return new JsonResponse($result);
    }

    /**
     * Check compatibility product
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function compatibility(Request $request, $id)
    {
        /** @var Product $product */
        if (!$product = $this->productRepository->find($id)) {
            return new JsonResponse('Product not found', 404);
        }

        $session = new Session();
        $sessionProducts = $session->get('vincheck');

        if ($sessionProducts !== null) {
            $this->get('app.vin_check.compatibility_products')
                ->setOneCompatibility($product, $sessionProducts['products'], $sessionProducts['compatibility']);
            $this->get('app.vin_check.compatibility_products')
                ->popupOptionCompatibility($product, $sessionProducts['popupOption']);
        }

        $result = ['compatibility' => $product->getCompatibility() ? $product->getCompatibility() : null];

        $result['buyers_guide']['products'] = $product->getBuyersRelated()->count() >= 1 ? (new BuyersGuideNormalizer())
            ->normalize($product) : [];
        $result['popup_option'] = (new PopupOptionNormalizer())->normalize($product);
        $result['addons'] = (new AddonsOptionNormalizer())->normalize($product, null, ['type' => 'Addons']);

        return new JsonResponse($result);
    }

    /**
     * Get product properties by type
     *
     * @param Request $request
     * @param $id
     * @param $type
     * @return JsonResponse
     */
    public function productProperty(Request $request, $id, $type)
    {

        /** @var Product $product */
        if (!$product = $this->productRepository->find($id)) {
            return new JsonResponse('Product not found', 404);
        }
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());

        $context = [
            'channel' => $channel,
            'session' => $request->getSession(),
            'compatibilityService' => $this->get('app.vin_check.compatibility_products')
        ];

        if (class_exists($class = 'AppBundle\\Serializer\\Normalizer\\' . ucfirst($type) . 'Normalizer')) {
            $normalizer = (new $class())->normalize($product, 'json', $context);
            return new JsonResponse([$type => $normalizer], 200);
        }
        return new JsonResponse('Data not found', 404);
    }

    /**
     * Get products for product association
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAssociation(Request $request)
    {
        $query = $request->query->get('criteria')['search']['value'] ?: '';
        $result = $this->productRepository->findByNamePartLimit($query, 'en_US', 10);
        $products = ['_embedded' => [
            'items' => []
        ]];

        /** @var Product $product */
        foreach ($result as $product) {
            if ($request->get('product') != $product->getId()) {
                $products['_embedded']['items'][] = [
                    'id' => $product->getId(),
                    'code' => $product->getCode(),
                    'name' => $product->getName() . '; ID: ' . $product->getCode()
                ];
            }
        }

        return new JsonResponse($products);
    }

    /**
     *  Get product addons
     *
     * @param  Collection $associations
     * @param $type
     * @return mixed
     */
    private function getAddons(Collection $associations, $type)
    {
        $products = $associations->filter(function ($association) use ($type) {
            /** @var ProductAssociation $association */
            if ($association->getType()->getCode() == $type) {
                return $association;
            }
        })->first();

        if ($products) {
            $products = $products->getAssociatedProducts()
                ->toArray();

            return array_map(function ($product) {
                return $this->normalizer->normalize($product, 'json', ['channel' => $this->addonsChannel]);
            }, $products);
        }
        return [];
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function interestingInProduct(Request $request, $id)
    {

        /** @var Product $product */

        if (!$product = $this->productRepository->find($id)) {
            return new JsonResponse('Product not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $products = $product->getInterestingProducts()->toArray();
        $products = $this->get('app.service.interesting_in_compatible')->getCompatible($products, $request->getSession());
        $channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());

        $data = array_map(function ($product) use ($channel){

            /** @var Product $product */
            $product = (new InterestedInNormalizer)->normalize($product, null, ['channel' => $channel]);
            return $product;
        }, $products);


        return new JsonResponse(array_values($data));
    }

}
