<?php


namespace AppBundle\Form\Type;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class OrderItemCodingType
 * @package AppBundle\Form\Type
 */
class OrderItemCodingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codingStatus', ChoiceType::class, [
            'label' => false,
            'choices' => [
                'Not coded' => 'not coded',
                'Complete' => 'complete'
            ]
        ])->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $data = $this->checkStatuses($event->getData());
            $event->setData($data);
        });
    }

    /**
     *
     * @param OrderItem $orderItem
     * @return OrderItem
     */
    protected function checkStatuses(OrderItem $orderItem)
    {
        /** @var Order $order */
        $order = $orderItem->getOrder();

        /** @var OrderItem $item */
        foreach ($order->getItems() as $item) {
            if ($item->getCodingStatus() != $item::NOT_REQUIRED && $item->getCodingStatus() != $item::COMPLETE) {
                $order->setCodingStatus($item::PARTIALLY_CODED);
            } else {
                $order->setCodingStatus($item::COMPLETE);
            }
        }
        return $orderItem;
    }
}