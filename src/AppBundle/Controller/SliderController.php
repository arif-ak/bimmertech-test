<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Slide;
use AppBundle\Repository\SliderRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SliderController extends Controller
{

    /**
     * @var SliderRepository
     */
    private $objectManager;

    /**
     * SliderController constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get menu with taxons and product
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $code = $request->attributes->get('slider');
        $slides = $this->objectManager
            ->getRepository('AppBundle:Slide')
            ->getBySliderCode($code);

        $forms = [];

        /** @var Slide $slide */
        foreach ($slides as $slide) {
            if ($slide->getProduct()) {
                $forms[] = $this->createForm(AddToCartType::class, null, ['product' => $slide->getProduct()])->createView();
            } else {
                $forms[] = null;
            }
        }

        return $this->render(':Slide:slider.html.twig', [
            'slides' => $slides,
            'forms' => $forms
        ]);
    }
}
