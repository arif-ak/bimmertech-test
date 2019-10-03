<?php

namespace AppBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountController
 *
 * @package AppBundle\Controller
 */
class AccountController extends ResourceController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param EngineInterface $templatingEngine
     */
    public function __construct(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }

    public function deleteAccountAction(Request $request): Response
    {
        if ($request->isMethod('POST') && $request->request->has('deactivate')) {
            $user = $this->getUser();
            $shopUser = $this->container->get('sylius.repository.shop_user')->find($user->getId());
            if ($shopUser instanceof ShopUser) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($shopUser);

                $em->flush();

                $this->redirectToRoute('sylius_shop_login');
            }
        }

        return $this->templatingEngine->renderResponse('SyliusShopBundle:Account/Delete:deactivate.html.twig');
    }
}
