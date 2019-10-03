<?php

namespace AppBundle\Controller;


use AppBundle\Entity\HelpPage;
use AppBundle\Form\Type\HelpSeoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HelpSeoController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function helpSeo(Request $request)
    {
        if (!$helpPage = $this->get('app.repository.help_page')->findOneByTitle('help')) {
            $helpPage = new HelpPage();
            $helpPage->setTitle('Help');
        }

        $form = $this->createForm(HelpSeoType::class, $helpPage);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($helpPage);
            $em->flush();


            return $this->redirectToRoute('app_admin_help_seo');
        }

        return $this->render('Pages/Help/edit.html.twig', [

            'form' => $form->createView(),
        ]);
    }
}