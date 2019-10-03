<?php

namespace AppBundle\Command;

use AppBundle\Entity\Order;
use AppBundle\Repository\OrderRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrderStateCommand
 * @package AppBundle\Command
 */
class OrderStateCommand extends Command
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * OrderStateCommand constructor.
     * @param OrderRepository $orderRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(OrderRepository $orderRepository, ObjectManager $objectManager)
    {
        parent::__construct('app:order-state');
        $this->orderRepository = $orderRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * Set order state canceled after one month if order not paid
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTime();
        $date->modify('-1 month');

        $orders = $this->orderRepository->createQueryBuilder('o')
            ->where('o.checkoutCompletedAt < :date')
            ->andWhere('o.paymentState = :state')
            ->setParameter('date', $date)
            ->setParameter('state', 'awaiting_payment')
            ->getQuery()
            ->getResult();

        if ($orders) {
            /** @var Order $order */
            foreach ($orders as $order) {
                $order->setState($order::STATE_CANCELLED);
                $this->objectManager->persist($order);
            }
            $this->objectManager->flush();
        }
    }

    protected function configure()
    {
        $this->setDescription('Set order state canceled after one month if order not paid')
            ->setHelp('This command allows you to set status cancelled');
    }
}
