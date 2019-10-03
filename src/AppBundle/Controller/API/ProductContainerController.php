<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\PopupOption;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\Taxon;
use AppBundle\Repository\ProductRepository;
use AppBundle\Serializer\Normalizer\BuyersGuideNormalizer;
use AppBundle\Serializer\Normalizer\PopupOptionNormalizer;
use AppBundle\Serializer\Normalizer\ProductContainerNormalizer;
use AppBundle\Serializer\Normalizer\ProductNormalizer;
use AppBundle\Serializer\Normalizer\TaxonPopupOptionNormalizer;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\Channel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ProductContainerController
 *
 * @package AppBundle\Controller\API
 */
class ProductContainerController extends Controller
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * Get product by id
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function compatibility(Request $request)
    {
        $this->channel = $this->get('sylius.repository.channel')->findOneByHostname($request->getHost());
        $url = $this->channel->getHostname();

        $session = new Session();
        $sessionProducts = $session->get('vincheck');

        if ($taxonCode = $request->request->get('slug')) {
            /** @var Taxon $taxon */
            $taxon = $this->get('sylius.repository.taxon')->findOneBySlug($taxonCode, 'en_US');
            if ($taxon) {
                if ($taxon->getLevel() == Taxon::TAXON_CONTAINER) {
                    if ($sessionProducts !== null) {
                        $products = $taxon->getProducts();

                        $this->get('app.vin_check.compatibility_products')
                            ->taxonPopupOptionCompatibility($taxon, $sessionProducts['popupOption']);

                        /** @var Product $product */
                        foreach ($products as $product) {
                            $this->get('app.vin_check.compatibility_products')
                                ->setOneCompatibility($product, $sessionProducts['products'], $sessionProducts['compatibility']);
                            if ($product->getCompatibility() == ProductInterface::COMPATIBILITY_YES) {
                                return new JsonResponse(
                                    [
                                        'compatibility' => Product::COMPATIBILITY_YES,
                                        'url' => $request->getHost(),
                                        'product' => (new ProductContainerNormalizer())->normalize($product),
                                        'popup_option' => (new TaxonPopupOptionNormalizer)->normalize($taxon)
                                    ]
                                );
                            }
                        }
                    }

                    if ($sessionProducts !== null) {
                        $compatibility = $sessionProducts['compatibility'] == Product::COMPATIBILITY_NOT_SURE ?
                            Product::COMPATIBILITY_NOT_SURE : Product::COMPATIBILITY_NO;
                    }

                    return new JsonResponse(
                        [
                            'compatibility' => isset($compatibility) ? $compatibility : null,
                            'url' => $url,
                            'product' => null,
                            'popup_option' => (new TaxonPopupOptionNormalizer)->normalize($taxon)
                        ]
                    );
                } else {
                    return new JsonResponse('This is not a container page', 400);
                }
            }
        }

        return new JsonResponse('Required parameter "slug" container page', 400);
    }
}
