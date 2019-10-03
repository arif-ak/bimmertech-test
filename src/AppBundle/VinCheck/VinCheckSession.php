<?php

namespace AppBundle\VinCheck;

use AppBundle\Entity\PopupOption;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductInterface;
use AppBundle\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociation;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class VinCheckSession
 * @package AppBundle\VinCheck
 */
class VinCheckSession
{
    /**
     * @var VinCheck
     */
    private $vincheck;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRep;

    /**
     * VinCheckSession constructor.
     *
     * @param VinCheck $vincheck
     * @param ProductRepository $productRep
     */
    public function __construct(VinCheck $vincheck, ProductRepository $productRep)
    {
        $this->vincheck = $vincheck;
        $this->productRep = $productRep;
    }

    /**
     * @param string $vin
     * @param ChannelInterface $channel
     * @param string $locale
     * @return Session
     */
    public function saveToSessionByVin(string $vin, ChannelInterface $channel, string $locale = 'en_US')
    {
        $compatibility = ProductInterface::COMPATIBILITY_YES;

        $vincheckResult = $this->vincheck->getCompatibilityProducts($vin);

        $vinCar = $vincheckResult['vinData']['vin'];
        $modelCar = substr($vincheckResult['vinData']['E series'], 0, 3);
        $yearCar = substr($vincheckResult['vinData']['Prod.date'], 0, 4);
        $series = $vincheckResult['vinData']['Series'];

        $label = $series != 'M' ? 'BMW ' . $yearCar . ' ' . $series . '-Series' : 'MINI ' . substr($vincheckResult['vinData']['Model'], 0, 8);

        $products = $this->productRep->findByEnabled(true);

        $vinProducts = isset($vincheckResult['products']) ? $vincheckResult['products'] : [];

        $sessionParms = $this->generateParams($compatibility, $products, $vinCar, $modelCar, $yearCar, $label, $series, $vinProducts);

        $session = new Session();

        $session->set('vincheck', $sessionParms);

        return $session;
    }

    /**
     * @param string $model
     * @param string $year
     * @param ChannelInterface $channel
     * @param string $locale
     * @param null $label
     */
    public function saveToSessionByManual(string $model, string $year, ChannelInterface $channel,
                                          string $locale = 'en_US', $label = null)
    {
        $compatibility = ProductInterface::COMPATIBILITY_NOT_SURE;
        $vincheckResult = $this->vincheck->getManualProducts($model, $year);
        $vinCar = null;
        $modelCar = substr($vincheckResult['vinData']['E series'], 0, 3);
        $yearCar = substr($vincheckResult['vinData']['Prod.date'], 0, 4);

        $series = isset($vincheckResult['vinData']['Series']) ?: null;

        $products = $this->productRep->getProductByChannel($channel, $locale);
        $vinProducts = isset($vincheckResult['products']) ? $vincheckResult['products'] : [];

        $sessionParms = $this->generateParams($compatibility, $products, $vinCar, $modelCar, $yearCar, $label, $series, $vinProducts);

        $session = new Session();
        $session->set('vincheck', $sessionParms);
    }

    /**
     * @param $compatibility
     * @param $products
     * @param null $vincheckProducts
     * @param $vinCar
     * @param $modelCar
     * @param $yearCar
     * @param null $label
     * @param null $series
     * @return array
     */
    private function generateParams($compatibility, $products, $vinCar, $modelCar, $yearCar, $label = null, $series = null, $vincheckProducts = null)
    {

        $sessionArr = [
            'compatibility' => $compatibility,
            'vin' => $vinCar,
            'model' => $modelCar,
            'year' => $yearCar,
            'label' => $label,
            'series' => $series
        ];

        $compatibilityProducts = [];
        $popupOptionArray = [];

        foreach ($vincheckProducts as $vinProduct) {
            /** @var Product $product */
            foreach ($products as $product) {
                $vinId = $product->getVinCheckProductId();

                if ($vinProduct['id'] === $vinId) {
                    $compatibilityProduct['productId'] = $product->getId();
                    $compatibilityProduct['code'] = $product->getCode();
                    $compatibilityProduct['vincheckId'] = $product->getVinCheckProductId();

                    $addons = [];
                    $prodAssoc = $product->getAssociations();
                    /** @var ProductAssociation $association */
                    foreach ($prodAssoc as $association) {
                        if ($association->getType()->getCode() === 'Addons') {

                            $addonProducts = $association->getAssociatedProducts();
                            $addons = $this->getAddons($addonProducts, $vincheckProducts);

                        }
                    }
                    $compatibilityProduct['addons'] = $addons;
                    array_push($compatibilityProducts, $compatibilityProduct);
                }
            }

            $popupKeyArray = [PopupOption::NPT, PopupOption::PDC];

            if (in_array((int)$vinProduct['id'], $popupKeyArray)) {
                $value = $vinProduct['name'];

                $popupOptionArray[] = [
                    "id" => $vinProduct['id'],
                    'compatibility' => $vinProduct[$value],
                    $vinProduct['name'] => $vinProduct[$value]
                ];
            }
        }

        $sessionArr['popupOption'] = $popupOptionArray;
        $sessionArr['products'] = $compatibilityProducts;
        return $sessionArr;
    }

    /**
     * Vincheckservice key = product code
     *
     * @param Collection $products
     * @param $vincheckProducts
     * @return array
     */
    private function getAddons(Collection $products, $vincheckProducts)
    {
        $addons = [];
        foreach ($vincheckProducts as $value) {
            /** @var Product $product */
            foreach ($products as $product) {
                if ($product->getVinCheckProductId() == $value['id']) {
                    array_push($addons, [
                        'code' => $product->getCode(),
                        'productId' => $product->getId(),
                        'vincheckId' => $product->getVinCheckProductId(),
                    ]);
                }
            }
        }
        return $addons;
    }
}
