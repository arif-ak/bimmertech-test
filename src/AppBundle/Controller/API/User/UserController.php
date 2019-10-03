<?php

namespace AppBundle\Controller\API\User;

use AppBundle\Entity\Customer;
use AppBundle\Entity\ShopUser;
use AppBundle\Repository\OrderItemRepository;
use AppBundle\Repository\OrderRepository;
use AppBundle\Serializer\Normalizer\User\CodingNormalizer;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\AdminApiBundle\Form\Type\CustomerProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * Class ConfirmationPageController
 * @package AppBundle\Controller\API\User
 */
class UserController extends Controller
{
    /**
     * @var
     */
    protected $objectManager;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * ConfirmationPageController constructor.
     *
     * @param ObjectManager $objectManager
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        ObjectManager $objectManager,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->objectManager = $objectManager;
        $this->encoderFactory = $encoderFactory;
    }

    public function personalInfo(Request $request)
    {
        /** @var ShopUser $user */
        if (!$user = $this->getUser()) {
            return new JsonResponse('You are no authorised', JsonResponse::HTTP_FORBIDDEN);
        }
        /** @var Customer $customer */
        $customer = $user->getCustomer();

        $form = $this->createForm(CustomerProfileType::class, $customer, ['csrf_protection' => false]);
        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        if (!$form->isValid()) {
            return new JsonResponse('form validation false', JsonResponse::HTTP_BAD_REQUEST);
        }
        $this->objectManager->persist($customer);
        $this->objectManager->flush();

        $data = [
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'phoneNumber' => $customer->getPhoneNumber(),
            'birthday' => $customer->getBirthday()->format('Y-m-d'),
            'vinNumber' => $customer->getVinNumber(),
            'company' => $customer->getCompany(),
            'vatNumber' => $customer->getVatNumber(),
            'gender' => $customer->getGender()
        ];

        $this->sendMail($customer->getEmail(), $data, 'shop_user_edit_info');

        return new  JsonResponse($data, JsonResponse::HTTP_OK);
    }

    /**
     * Change shop user password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request)
    {
        /** @var ShopUser $user */
        if (!$user = $this->getUser()) {
            return new JsonResponse('You are no authorised', JsonResponse::HTTP_FORBIDDEN);
        }

        if (!$this->validUser($request->get('password'))) {
            return new JsonResponse('Bad password', JsonResponse::HTTP_FORBIDDEN);
        }

        if (!$newPassword = $request->get('newPassword')) {
            return new JsonResponse('New password is wrong', JsonResponse::HTTP_BAD_REQUEST);
        }

        $encodedPassword = (new MessageDigestPasswordEncoder())->encodePassword($newPassword, $user->getSalt());
        $user->setPassword($encodedPassword);
        $this->objectManager->flush();

        $this->sendMail($user->getEmail(), [], 'shop_user_change_password');

        return new JsonResponse('Password changed', JsonResponse::HTTP_OK);
    }

    /**
     *  Disable shop user account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function closeAccount(Request $request)
    {
        /** @var ShopUser $user */
        if (!$user = $this->getUser()) {
            return new JsonResponse('You are no authorised', JsonResponse::HTTP_FORBIDDEN);
        }

        if (!$this->validUser($request->get('password'))) {
            return new JsonResponse('Bad password', JsonResponse::HTTP_FORBIDDEN);
        }

        $user->setEnabled(false);
        $user->setIsClosedAccount(true);
        $this->objectManager->flush();

        if (!$user->isEnabled()) {
            $user->eraseCredentials();
            $this->sendMail($user->getEmail(), ['shopUser'=>$user], 'shop_user_close_account');
            return new JsonResponse('Account closed', JsonResponse::HTTP_OK);
        }
    }

    /**
     * @return JsonResponse
     */
    public function coding()
    {
        /** @var ShopUser $user */
        if (!$user = $this->getUser()) {
            return new JsonResponse('You are no authorised', JsonResponse::HTTP_FORBIDDEN);
        }

        /** @var OrderItemRepository $orderItemsRepository */
        $orderItemsRepository = $this->get('sylius.repository.order_item');
        if (!$items = $orderItemsRepository->getCodingByCustomer($user->getCustomer())) {
            return new  JsonResponse([], JsonResponse::HTTP_OK);
        }

        $data = (new CodingNormalizer('https://www.bimmer-tech.net/RemoteSession'))->normalize($items);

        return new  JsonResponse($data, JsonResponse::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orders(Request $request)
    {
        /** @var ShopUser $user */
        if (!$user = $this->getUser()) {
            return new JsonResponse('You are no authorised', JsonResponse::HTTP_FORBIDDEN);
        }

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');
        $orders = $orderRepository->findByCustomer($user->getCustomer());
        $array = [];

        foreach ($orders as $item) {
            $array[] = $this->get("app.serializer_normalizer.order_normalizer")->
            normalize($item, null, ['host' => $request->getSchemeAndHttpHost()]);

            rsort($array);
        }

        return new  JsonResponse($array, JsonResponse::HTTP_OK);
    }

    /**
     * @param $email
     * @param $data
     * @param $type
     */
    protected function sendMail($email, $data, $type)
    {
        $helper = $this->get('app.service.create_shop_user');
        $helper->sendMail($email, $data, $type);
    }

    /**
     * Check user password
     *
     * @param $password
     * @return mixed
     */
    private function validUser($password)
    {
        $user = $this->getUser();
        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show()
    {
        if ($this->getUser()) {
            return $this->render(":Pages/Account:index.html.twig");
        }
        return $this->redirect('/');
    }
}
