<?php

namespace AppBundle\Repository;

use AppBundle\Entity\DropDown;
use AppBundle\Entity\OrderItemInterface;
use AppBundle\Entity\ProductInterface;
use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserSupport;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class OrderRepository
 *
 * @package AppBundle\Repository
 */
class OrderRepository extends BaseOrderRepository
{
    /**
     * @var
     */
    protected $securityContext;

    /**
     * @param AuthorizationCheckerInterface $securityContext
     */
    public function setContainer(AuthorizationCheckerInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param $id
     *
     * @return null|OrderInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCartForSelectingTaxes($id): ?OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameter('id', $id)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->getQuery()
            ->getOneOrNullResult();

        $this->associationHydrator->hydrateAssociations($order, [
            'items',
            'items.variant',
            'items.variant.product',
            'items.variant.product.translations',
        ]);

        return $order;
    }

    /**
     * @return QueryBuilder
     */
    public function isRepositoryChanged()
    {
        $order = $this->createQueryBuilder('o')
            ->addSelect('channel')
            ->addSelect('customer')
            ->innerJoin('o.channel', 'channel')
            ->leftJoin('o.customer', 'customer')
            ->andWhere('o.state != :state')
            ->setParameter('state', OrderInterface::STATE_CART);

        return $order;
    }

    /**
     * @param $role
     * @return array
     */
    public function getUserRoleOrders($role)
    {
        $order = $this->createQueryBuilder('o')
            ->addSelect('channel')
            ->addSelect('customer')
            ->innerJoin('o.channel', 'channel')
            ->leftJoin('o.customer', 'customer')
            ->andWhere('o.state != :state')
            ->setParameter('state', OrderInterface::STATE_CART);

        if ($this->securityContext->isGranted(UserLogistic::LOGISTIC_ROLE)) {
            $city = null;
            if ($role == 'ROLE_LOGISTIC_ACCESS_DUBAI') {
                $city = 'Dubai';
            }
            if ($role == 'ROLE_LOGISTIC_ACCESS_WARSAW') {
                $city = 'Warsaw';
            }
            if ($role == 'ROLE_LOGISTIC_ACCESS_ORLANDO') {
                $city = 'Orlando';
            }

            $order
                ->andWhere('o.paymentState = :payment_state')
                ->innerJoin('o.items', 'oi')
                ->innerJoin('oi.units', 'oiu')
                ->innerJoin('oi.variant', 'pv')
                ->innerJoin('oiu.warehouse', 'oiuw')
                ->innerJoin('pv.product', 'pr')
                ->andWhere('oiuw.city = :city')
                ->setParameter('city', $city)
                ->setParameter('payment_state', 'completed')
                ->orderBy('o.id')
                ->orderBy('o.shippingState');
        }

        if ($role == UserSupport::SUPPORT_ROLE) {
            $order
                ->innerJoin('o.items', 'oi')
                ->innerJoin('oi.variant', 'pv')
                ->andWhere('o.paymentState = :payment_state')
                ->andWhere("pv.instructionRequired = :instructionRequired")
                ->andWhere('o.supportStatus != :status')
                ->setParameter('payment_state', 'completed')
                ->setParameter('instructionRequired', true)
                ->setParameter('status', OrderItemInterface::NOT_REQUIRED);
        }

        if ($role == UserCoding::CODING_ROLE) {

            $order
                ->andWhere('o.paymentState = :payment_state')
                ->innerJoin('o.items', 'oi')
                // product variant
                ->innerJoin('oi.variant', 'pv')
                // get drop down
                ->leftJoin('oi.orderItemDropDownOptions', 'oidr')
                ->leftJoin('oidr.dropDownOption', 'ddo')
                ->leftJoin('ddo.dropDown', 'dd')
                // product
                ->innerJoin('pv.product', 'pr')
                ->andWhere('o.codingStatus != :status')
                ->andWhere('pr.type = :type OR pv.hasSoftware = :hasSoftware OR dd.type = :typeCoding OR dd.type = :typePhysicalCoding')
                ->setParameter('payment_state', 'completed')
                ->setParameter('type', ProductInterface::TYPE_CODING)
                ->setParameter('hasSoftware', true)
                ->setParameter('status', OrderItemInterface::NOT_REQUIRED)
                ->setParameter('typeCoding', DropDown::CODDING_PRODUCT)
                ->setParameter('typePhysicalCoding', DropDown::PHYSICAL_PRODUCT_WITH_CODDING);

        }

        return $order->getQuery()->getResult();
    }
}
