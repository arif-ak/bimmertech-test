<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ConfirmationPageController
 *
 * @package AppBundle\Controller
 */
class ConfirmationPageController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('Pages/Confirmation.htm.twig');
    }
}