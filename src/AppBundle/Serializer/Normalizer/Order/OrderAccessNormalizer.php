<?php

namespace AppBundle\Serializer\Normalizer\Order;

use AppBundle\Entity\AdminUser;
use AppBundle\Entity\OrderNote;
use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserMarketing;
use AppBundle\Entity\UserSale;
use AppBundle\Entity\UserSupport;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sylius\Component\User\Model\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class NoteNormalizer
 */
class OrderAccessNormalizer implements NormalizerInterface
{
    /**
     * @var
     */
    protected $securityContext;

    /**
     * ElasticSearchService constructor.
     *
     * @param AuthorizationCheckerInterface $securityContext
     */
    public function __construct(
        AuthorizationCheckerInterface $securityContext
    ) {
        $this->securityContext = $securityContext;
    }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $user = $object;
        $access = [
            "vin_edit" => false,
            "shipping_status" => false,
            "order_status" => false,
            "order_refund" => false,
            "order_refund_edit" => false,
            "add_note" => false,
            "history_logs" => false,
            "logistic_board" => false,
            "usb_coding_board" => false,
            "support_board" => false,
            "codding_board" => false,
        ];

        if ($this->securityContext->isGranted(AdminUser::DEFAULT_ADMIN_ROLE)) {
            $access = [
                "vin_edit" => true,
                "shipping_status" => true,
                "order_status" => true,
                "add_note" => true,
                "history_logs" => true,
                "logistic_board" => true,
                "usb_coding_board" => true,
                "support_board" => true,
                "codding_board" => true,
                "order_refund" => true,
                "order_refund_edit" => true,
            ];

            return $access;
        }

        if ($this->securityContext->isGranted(UserLogistic::LOGISTIC_ROLE)) {
            $access = [
                "vin_edit" => true,
                "shipping_status" => true,
                "order_status" => true,
                "order_refund" => false,
                "order_refund_edit" => true,
                "add_note" => true,
                "history_logs" => true,
                "logistic_board" => true,
                "usb_coding_board" => true,
                "support_board" => false,
                "codding_board" => false,
            ];

            return $access;
        }

        if ($this->securityContext->isGranted(UserSupport::SUPPORT_ROLE)) {
            /**@var UserSupport $user */
            if ($user && $user->getRefundAccess()) {
                $access['order_refund'] = true;
            }

            $access["order_refund_edit"] = true;
            $access['vin_edit'] = true;
            $access['add_note'] = true;
            $access["usb_coding_board"] = true;
            $access["support_board"] = true;
            $access['shipping_status'] = true;
            $access['order_status'] = true;
            $access['history_logs'] = true;

            return $access;
        }

        if ($this->securityContext->isGranted(UserCoding::CODING_ROLE)) {
            $access['add_note'] = true;
            $access['codding_board'] = true;
            $access["order_refund_edit"] = true;
            $access['usb_coding_board'] = true;
            $access['history_logs'] = true;

            return $access;
        }

        if ($this->securityContext->isGranted(UserMarketing::MARKETING_ROLE)) {
            return $access;
        }

        if ($this->securityContext->isGranted(UserSale::SALE_ROLE)) {
            $access['vin_edit'] = true;
            $access['add_note'] = true;
            $access['shipping_status'] = true;
            $access['order_status'] = true;

            return $access;
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }
}
