<?php

namespace AppBundle\Controller;

use AppBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ClosePaymentPageController
 * @package AppBundle\Controller
 */
class ClosePaymentPageController extends Controller
{
    /**
     * Close page window
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function closeAction(Request $request)
    {
        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');
        $token = $request->get('tokenValue');
        if ($order = $orderRepository->findOneByTokenValue($token)) {
            $helper = $this->get('app.service.after_payment');
            $roles = ['ROLE_SALE_ACCESS', 'ROLE_ADMINISTRATION_ACCESS'];
            $dataProducts = $this->container->get('app.service.check_item_type')->checkType($order);

            $this->container->get('app.service.admin_user')
                ->sendEmail($roles, 'app_after_order_created', ['order' => $order, 'dataProducts' => $dataProducts]);

            $helper->paymentClosed($order);
        }
        return $this->render('ClosePage.html.twig');
    }

}