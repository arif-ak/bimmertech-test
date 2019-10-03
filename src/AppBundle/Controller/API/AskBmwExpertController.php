<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\GeneralOption;
use AppBundle\Repository\GeneralOptionRepository;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AskBmwExpertController
 *
 * @package AppBundle\Controller\API
 */
class AskBmwExpertController extends Controller
{
    /**
     * @var SenderInterface
     */
    private $sender;

    /**
     * @var GeneralOptionRepository
     */
    private $generalOptionRepository;
    
    /**
     * AskBmwExpertController constructor.
     *
     * @param SenderInterface $sender
     * @param GeneralOptionRepository $generalOptionRepository
     */
    public function __construct(SenderInterface $sender, GeneralOptionRepository $generalOptionRepository)
    {
        $this->sender = $sender;
        $this->generalOptionRepository = $generalOptionRepository;
    }

    /**
     * Send for Ask BMW expert
     *
     * @param Request $request
     * @param $type
     * @return JsonResponse
     */
    public function sendMessage(Request $request, $type)
    {
        /** @var GeneralOption $option */
        if (!$option = $this->generalOptionRepository->findOneByKey($type)) {
            return new JsonResponse('No setting ' . $type, 404);
        }
        $data = [
            'data' => $request->request->all(),
            'subject' => 'Ask BMW Expert - ' . $option->getName()
        ];

        $this->sendMail($option->getValue(), $data, 'shop_product_ask_bmw_expert');

        return new JsonResponse('Question sended', 200);
    }

    /**
     *  Send password and confirm email link
     *
     * @param $email
     * @param array $data
     * @param $type
     */
    private function sendMail($email, array $data, $type)
    {
        if ($email) {
            $this->sender->send($type, [$email], $data);
        }
    }
}