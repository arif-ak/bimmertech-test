<?php

namespace AppBundle\Serializer\Normalizer\OrderItem;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\DropDownOption;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\OrderItemUnit;
use AppBundle\Entity\OrderItemUsbCoding;
use AppBundle\Serializer\Normalizer\SavePriceNormalizer;
use AppBundle\Service\OrderRefundService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderItemNormalizer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * PayPalService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var OrderItem $item */
        $item = $object;
        $vin = $item->getOrder()->getVin();
        $vinId = $item->getVariant()->getVincheckserviceId();

        $supportBoard = isset($context['support_board']) ? $context['support_board'] : null;
        $codingBoard = isset($context['codding_board']) ? $context['codding_board'] : null;
        $usbCoding = isset($context['usb_coding']) ? $context['usb_coding'] : null;

        $image = '/images/no-image.png';
        if ($item->getProduct()->getImages()->count()) {
            $image = '/media/cache/resolve/bord_78_50/' . $item->getProduct()->getImages()->first()->getPath();
        }

        $units = $item->getUnits();

        $data = [
            'id' => $item->getId(),
            'quantity' => isset($context['usb_coding']) && $item->getOrderItemUsbCoding() ? 0 : 1 ,
            'unitPrice' => $item->getUnitPrice(),
            'total' => $item->getTotal(),
            'name' => $item->getVariantName() ?: $item->getProductName(),
            'product_variant_id' => $item->getVariant()->getId(),
            'image' => $image,
            'savePrice' => (new SavePriceNormalizer())->normalize($item->getSavePrice()),
            'order_item_unit_refund' => $this->container->get("app.service.order_refund")->getOrderRefund($units),
            'order_item_unit_return' => $this->container->get("app.service.order_refund")->getOrderReturn($units)
        ];

        if ($supportBoard) {
            if (!empty($item->getInstruction())) {
                $instruction = $item->getInstruction();
            } elseif ($item->getVariant()->getInstruction()) {
                $instruction = $item->getVariant()->getInstruction();
            }

            $data['support_board'] = [
                'status' => $item->getSupportStatus(),
                'instruction' => isset($instruction) ? $instruction : ""
            ];
            $data['bopt'] = "/bopt?vin=$vin&productId=$vinId";
            $data['date'] = $item->getSupportDate() ? $item->getSupportDate()->format('Y-m-d') : null;
            $data['drop_down'] = [];
            $dropDowns = $this->getDropDowns($item);
            if (count($dropDowns) > 0) {
                $data['drop_down'] = $dropDowns;
            }
        }

        if ($codingBoard) {
            $data['date'] = $item->getCodingDate() ? $item->getCodingDate()->format('Y-m-d') : null;
            $data['drop_down'] = [];
            $dropDowns = $this->getDropDowns($item);
            if (count($dropDowns) > 0) {
                $data['drop_down'] = $dropDowns;
            }
            $data['coding'] = [
                'status' => $item->getCodingStatus(),
            ];
        }

        if ($usbCoding) {
            /** @var OrderItemUsbCoding $orderItemUsbCoding */
            $orderItemUsbCoding = $item->getOrderItemUsbCoding();
            $data['usb_coding'] = [
                'is_sent' => $orderItemUsbCoding ? $orderItemUsbCoding->isCoded() : false,
                'date' =>  $orderItemUsbCoding ?
                    $orderItemUsbCoding->getCreatedAt()->format('Y-m-d') : null
            ];
            $data['drop_down'] = [];
            $dropDowns = $this->getDropDowns($item);
            if (count($dropDowns) > 0) {
                $data['drop_down'] = $dropDowns;
            }
        }

        return $data;
    }

    /**
     * Get selected dropDown
     *
     * @param OrderItem $item
     * @return array
     */
    private function getDropDowns(OrderItem $item)
    {
        $dropDownOptions = [];
        if ($item->getOrderItemDropDownOptions()) {
            /** @var OrderItemDropDownOption $orderItemDropDownOption */
            foreach ($item->getOrderItemDropDownOptions() as $orderItemDropDownOption) {
                $downOption = $orderItemDropDownOption->getDropDownOption();
                /** @var DropDown $dropDown */
                $dropDown = $downOption->getDropDown();
                /** @var DropDownOption $downOption */
                if ($dropDown->getType() == DropDown::PHYSICAL_PRODUCT ||
                    $dropDown->getType() == DropDown::PHYSICAL_PRODUCT_WITH_CODDING ||
                    $dropDown->getType() == DropDown::NONE_PRODUCT  ||
                    $dropDown->getType() == DropDown::CODDING_PRODUCT
                ) {
                    $dropDownOptions[] = [
                        'id' => $orderItemDropDownOption->getId(),
                        'name' => $downOption->getDropDown()->getName() . ": " . $downOption->getName(),
                    ];
                }
            }
        }

        return $dropDownOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItem;
    }
}