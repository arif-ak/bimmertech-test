<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\DropDownOption;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\OrderItemDropDownOption;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\ProductInterface;
use AppBundle\Serializer\Normalizer\OrderItem\OrderItemNormalizer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CoddingBoardNormalizer
{
    /**
     * @var OrderItemNormalizer
     */
    private $orderItemNormalizer;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SupportBoardNormalizer constructor.
     */
    public function __construct(
        OrderItemNormalizer $orderItemNormalizer,
        ContainerInterface $container
    ) {
        $this->orderItemNormalizer = $orderItemNormalizer;
        $this->container = $container;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /** @var Order $order */
        $order = $object;
        $items = $order->getItems();

        $listOfOrderItemInstruction = [];
        /** @var OrderItem $item */
        foreach ($items as $item) {
            $productVariant = $item->getVariant();
            $product = $item->getProduct();
            if (($item->getType() != OrderItemInterface::TYPE_WARRANTY &&
                    $product->getType() == ProductInterface::TYPE_CODING)
                ||
                ($item->getType() != OrderItemInterface::TYPE_WARRANTY &&
                    $productVariant->getHasSoftware() == true)
                ||
                ($item->getType() != OrderItemInterface::TYPE_WARRANTY &&
                    $this->container->get('app.service.order_item_board_type_service')
                        ->checkDropDropDownTypeIsCoding($item)
                )
            ) {
                $listOfOrderItemInstruction['order_item'][] = $this->orderItemNormalizer
                    ->normalize($item, null, ['codding_board' => true]);
            }
        }

        return $listOfOrderItemInstruction;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderItem;
    }
}
