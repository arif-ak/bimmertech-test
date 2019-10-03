<?php

namespace AppBundle\Controller\Shop;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Dealer;
use AppBundle\Entity\FAQQuestion;
use AppBundle\Entity\Installer;
use AppBundle\Form\Type\FAQType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class InformationInFooterController
 *
 * @package AppBundle\Controller\Shop
 */
class InformationInFooterController extends Controller
{
//    const Order

    private $objectManager;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;


    /**
     * InformationInFooterController constructor.
     *
     * @param EngineInterface $templatingEngine
     * @param ObjectManager $objectManager
     */
    public function __construct(EngineInterface $templatingEngine, ObjectManager $objectManager)
    {
        $this->templatingEngine = $templatingEngine;
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function termsAndCondition()
    {
        if (!$page = $this->get('app.repository.custom_page')->findOneBySlug('terms-and-conditions')) {
            return $this->render('@SyliusShop/error404.html.twig', [], (new Response('page not found', Response::HTTP_NOT_FOUND)));
        }

        return $this->render('Pages/Page/Show.htm.twig', [
            'page' => $page
        ]);
    }

    public function paymentAndDelivery()
    {
        return $this->templatingEngine->renderResponse(":InformationInFooter:payment_and_delivery.html.twig");
    }

    public function installers()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Installer::class);
        $installers = $repository->findAll();

        return $this->templatingEngine->renderResponse(":InformationInFooter:installers.html.twig",
            ['installers' => $installers]);
    }

    public function contact(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $contactRepository = $em->getRepository(Contact::class);

        $contacts = $contactRepository->createQueryBuilder('c')
            ->orderBy('c.contactPosition', "ASC")
            ->getQuery()
            ->getResult();

        return $this->templatingEngine->
        renderResponse(":InformationInFooter:contact.html.twig", ['contacts' => $contacts]);
    }

    public function FAQ(Request $request)
    {
        $em = $this->objectManager;

        $form = $this->createForm(FAQType::class);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $data = $form->getData();
            $search = !empty($data['search']) ? true : false;
            if ($search) {
                $searchResult = $em->getRepository(FAQQuestion::class)->sortByHeader($data['search']);

                return $this->templatingEngine->renderResponse(":InformationInFooter:faq.html.twig", [
                    'searchResults' => $searchResult,
                    'faqs' => null,
                    'form' => $form->createView()
                ]);
            }
        }

        $faqArray = [];
        $faqs = $em->getRepository(FAQQuestion::class)->sortByHeader();
        /* @var FAQQuestion $faq */
        foreach ($faqs as $faq) {
            $faqArray[$faq->getHeader()][] = $faq;
        }

        return $this->templatingEngine->renderResponse(":InformationInFooter:faq.html.twig", [
            'searchResults' => null,
            'faqs' => $faqArray,
            'form' => $form->createView()
        ]);
    }

    public function privacyPolicy()
    {
        if (!$page = $this->get('app.repository.custom_page')->findOneBySlug('privacy-policy')) {
            return $this->render('@SyliusShop/error404.html.twig', [], (new Response('page not found', Response::HTTP_NOT_FOUND)));
        }

        return $this->render('Pages/Page/Show.htm.twig', [
            'page' => $page
        ]);
    }

    public function cookiesPolicy()
    {
        return $this->templatingEngine->renderResponse(":InformationInFooter:cookies_policy.html.twig");
    }

    public function forDealer()
    {
        $em = $this->getDoctrine()->getManager();
        $dealerRepository = $em->getRepository(Dealer::class);

        $dealers = $dealerRepository->createQueryBuilder('d')
            ->getQuery()
            ->getResult();

        if (count($dealers) > 0) {
            $dealer = $dealers[0];
        } else {
            $dealer = null;
        }

        return $this->templatingEngine->renderResponse(":InformationInFooter:for_dealer.html.twig",
            ['dealer' => $dealer]
        );
    }

    public function aboutUs()
    {
        return $this->templatingEngine->renderResponse(":InformationInFooter:about_us.html.twig");
    }

    public function help(Request $request)
    {
        $em = $this->objectManager;

        $form = $this->createForm(FAQType::class);
        $form->handleRequest($request);

        $sessionData = $request->getSession()->get('_faq_search');
        $data = $form->getData();

        if ($form->isValid() && $form->isSubmitted() && $sessionData != $data['search']) {

            $search = !empty($data['search']) ? true : false;
            $session = new Session();
            $session->set('_faq_search', $data['search']);

            if ($search) {
                $searchResult = $em->getRepository(FAQQuestion::class)->sortByHeader($data['search']);
                return $this->templatingEngine->renderResponse(":InformationInFooter:help.html.twig", [
                    'searchResults' => $searchResult,
                    'headers' => null,
                    'form' => $form->createView()
                ]);
            }
        }

        $help = $this->get('app.repository.help_page')->findOneByTitle('Help');

        $helpHeader = $this->get('app.repository.help_header')
            ->createQueryBuilder('h')
            ->addSelect('q')
            ->Join('h.questions', 'q', 'WITH', 'q.enabled = true')
            ->orderBy('h.position', 'ASC')
            ->getQuery()
            ->getResult();


        return $this->templatingEngine->renderResponse(":InformationInFooter:help.html.twig", [
            'searchResults' => null,
            'headers' => $helpHeader,
            'form' => $form->createView(),
            'help' => $help
        ]);
    }
}
