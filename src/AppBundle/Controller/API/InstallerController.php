<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Installer;
use AppBundle\Serializer\Normalizer\InstallerNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InstallerController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Installer::class);
        $installers = $repository->findAll();

        return new JsonResponse((new InstallerNormalizer())->normalize($installers));
    }

    public function locationAction(Request $request)
    {
        $result = $this->get('app.service.geolocation')->location($request);

        return new JsonResponse(['data' => $result]);
    }
}
