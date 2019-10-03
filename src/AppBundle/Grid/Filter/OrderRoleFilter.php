<?php

namespace AppBundle\Grid\Filter;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderItem;
use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserSupport;
use AppBundle\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class HelpHeaderFilter
 * @package AppBundle\Grid\Filter
 */
class OrderRoleFilter implements FilterInterface
{
    /**
     * @var
     */
    protected $securityContext;

    /**
     * @var OrderRepository
     */
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param AuthorizationCheckerInterface $securityContext
     */
    public function setContainer(AuthorizationCheckerInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param DataSourceInterface $dataSource
     * @param string $name
     * @param mixed $data
     * @param array $options
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if ($data['show'] == '0') {
            $orders = $this->repository->getUserRoleOrders($this->getRole());
//
//            if ($this->securityContext->isGranted(UserLogistic::LOGISTIC_ROLE)) {
//                $orders = (new ArrayCollection($orders))->filter(function ($order) {
//                    /** @var Order $order */
//
//                    $items = $order->getItems()->filter(function ($item) {
//                        /** @var OrderItem $item */
//                        if ($item->getProduct()->getType() == null) {
//                            return $item;
//                        }
//                    });
//
//                    if ($items->count() > 0) {
//                        return $order;
//                    }
//                })->toArray();
//            }

            $ids = array_map(function ($item) {
                return $item->getId();
            }, $orders);

            if (count($ids) > 0) {
                $exp = $dataSource->getExpressionBuilder()->in('id', $ids);


                $dataSource->restrict($exp);
            } else {
                $exp = $dataSource->getExpressionBuilder();
                $exp = $exp->lessThan('id', 1);

                $dataSource->restrict($exp);
            }
        }
    }

    /**
     * @return string|null
     */
    public function getRole()
    {
        $role = null;

        if ($this->securityContext->isGranted('ROLE_ADMINISTRATION_ACCESS')) {
            $role = 'ROLE_ADMINISTRATION_ACCESS';
        }

        if ($this->securityContext->isGranted('ROLE_LOGISTIC_ACCESS_DUBAI')) {
            $role = 'ROLE_LOGISTIC_ACCESS_DUBAI';
        }
        if ($this->securityContext->isGranted('ROLE_LOGISTIC_ACCESS_WARSAW')) {
            $role = 'ROLE_LOGISTIC_ACCESS_WARSAW';
        }
        if ($this->securityContext->isGranted('ROLE_LOGISTIC_ACCESS_ORLANDO')) {
            $role = 'ROLE_LOGISTIC_ACCESS_ORLANDO';
        }

        if ($this->securityContext->isGranted(UserSupport::SUPPORT_ROLE)) {
            $role = UserSupport::SUPPORT_ROLE;
        }
        if ($this->securityContext->isGranted(UserCoding::CODING_ROLE)) {
            $role = UserSupport::SUPPORT_ROLE;
        }

        return $role;
    }
}
