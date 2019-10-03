<?php

namespace AppBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class OrderItemInstructionType extends AbstractResourceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $event->getForm()
                ->add('instruction', TextType::class);
        })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $instruction = ltrim($event->getData()['instruction'], '_');
                $event->setData(['instruction' => $instruction]);
            });
    }
}
