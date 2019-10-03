<?php

namespace AppBundle\Command;

use AppBundle\Service\OrderEmailService;
use Sylius\Component\Mailer\Sender\Sender;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrderStateCommand
 * @package AppBundle\Command
 */
class SendEmailCommand extends Command
{
    /**
     * @var Sender
     */
    protected $sender;
    /**
     * @var
     */
    protected $orderEmailService;
    /**
     * @var
     */
    protected $host;

    /**
     * SendEmailCommand constructor.
     * @param Sender $sender
     * @param  OrderEmailService $orderEmailService
     * @param $host
     */
    public function __construct(Sender $sender, $orderEmailService, $host)
    {
        parent::__construct('app:send-email');
        $this->sender = $sender;
        $this->orderEmailService = $orderEmailService;
        $this->host = $host;
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
        $type = $input->getArgument('type');
        $email = $input->getArgument('email');
        $data = $input->getArgument('data');


        if ($type == 'app_abandonded_cart') {
            $data = $this->orderEmailService->getData($data);
            $data['host'] = $this->host;
            $this->sender->send($type, [$email], ['data' => $data]);

            return $output->writeln('Send');
        }

        $this->sender->send($type, [$email], ['data' => $data, 'host'=> $this->host]);
        return $output->writeln($input->getArguments());
    }

    protected function configure()
    {
        $this->setDescription('Send email')
            ->setName('app:send-email')
            ->setHelp('Send email')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::OPTIONAL)
            ->addArgument('data', InputArgument::OPTIONAL);
    }
}
