<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserCookieSettingController
 * @package AppBundle\Controller
 */
class UserCookieSettingController extends Controller
{
    /**
     * Accept use cookie
     *
     * @param Request $request
     * @return Response
     */
    public function checkSetting(Request $request)
    {


        $response = new Response('ok ', 200);


         $response->headers->setCookie(new Cookie('_accept_cookie', 'accept'));
         return $response;
    }
}