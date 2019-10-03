<?php

namespace AppBundle\Grid\Filter;

use AppBundle\Entity\UserCoding;
use AppBundle\Entity\UserLogistic;
use AppBundle\Entity\UserSupport;
use AppBundle\Repository\OrderRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class HelpHeaderFilter
 * @package AppBundle\Grid\Filter
 */
class OrderSortByStatusesFilter implements FilterInterface
{
    /**
     * @var
     */
    protected $securityContext;

    /**
     * @var OrderRepository
     */
    protected $repository;

    /** @var EntityManager */
    protected $em;

    /**
     * @param AuthorizationCheckerInterface $securityContext
     */
    public function setContainer(AuthorizationCheckerInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function setEm(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DataSourceInterface $dataSource
     * @param string $name
     * @param mixed $data
     * @param array $options
     */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        if ($data['sortByStatus'] == '0') {
            $sort = $this->getSortDirection($data);

            $exp = $dataSource->getExpressionBuilder();
            $exp->orderBy($sort["field"], $sort["direction"]);
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function getSortDirection($data)
    {
        $direction = "DESC";
        $field = "id";

        if (isset($data['sortByStatus'])) {
            if ($this->securityContext->isGranted('ROLE_ADMINISTRATION_ACCESS')) {
                if ($data['sortByStatus'] == '0') {
                    $direction = "DESC";
                    $field = "id";
                }
            }

            if ($this->securityContext->isGranted(UserLogistic::LOGISTIC_ROLE)) {
                if ($data['sortByStatus'] == '0') {
                    $direction = "ASC";
                }
                $field = "shippingState";
            }

            if ($this->securityContext->isGranted(UserSupport::SUPPORT_ROLE)) {
                if ($data['sortByStatus'] == '0') {
                    $direction = "ASC";
                }
                $field = "shippingState";
            }

            if ($this->securityContext->isGranted(UserCoding::CODING_ROLE)) {
                if ($data['sortByStatus'] == '0') {
                    $direction = "ASC";
                }
                $field = "codingStatus";
            }
        }

        return ["direction" => $direction, "field" => $field];
    }
}
