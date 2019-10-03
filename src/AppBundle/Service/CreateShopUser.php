<?php

namespace AppBundle\Service;

use AppBundle\Entity\Customer;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * Class CreateShopUser
 * @package AppBundle\Service
 */
class CreateShopUser
{
    /**
     * @var FactoryInterface
     */
    private $shopUserFactory;

    /***
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var UserRepository;
     */
    private $shopUserRepository;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var SenderInterface
     */
    private $sender;

    /**
     * CreateShopUser constructor.
     *
     * @param FactoryInterface $shopUserFactory
     * @param ObjectManager $objectManager
     * @param UserRepository $shopUserRepository
     * @param SenderInterface $sender
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        FactoryInterface $shopUserFactory,
        ObjectManager $objectManager,
        UserRepository $shopUserRepository,
        SenderInterface $sender,
        CustomerRepository $customerRepository
    )
    {
        $this->shopUserFactory = $shopUserFactory;
        $this->objectManager = $objectManager;
        $this->shopUserRepository = $shopUserRepository;
        $this->sender = $sender;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Register ShopUser after created Customer
     *
     * @param $object
     * @throws \Exception
     */
    public function create($object)
    {

        /** @var Customer $customer */
        if (!$customer = $object->getCustomer()) {
            return;
        }
        if ($this->shopUserRepository->findOneByUsername($customer->getEmail())) {
            return;
        }

        $this->createNewUser($customer->getEmail(), $customer);
    }


    /**
     * Create new shop user
     *
     * @param $username
     * @param Customer|null $customer
     * @return bool|ShopUser
     * @throws \Exception
     */
    public function createNewUser($username, Customer $customer = null)
    {

        if ($this->shopUserRepository->findOneByUsername($username)) {
            return false;
        }

        if (!$customer && !$customer = $this->customerRepository->findOneByEmail($username)) {
            $customer = new Customer();

            $customer->setEmail($username);
            $customer->setEmailCanonical($username);
            $customer->setCreatedAt(new \DateTime('now'));
        }

        $password = $this->randomPassword();
        $hash = substr(md5(openssl_random_pseudo_bytes(20)), -20);

        /** @var \AppBundle\Entity\ShopUser $shopUser */
        $shopUser = $this->shopUserFactory->createNew();
        $shopUser->setPlainPassword($password);
        $shopUser->setIsClosedAccount(false);
        $shopUser->setEmailVerificationToken($hash);
        $shopUser->setUsername($username);
        $shopUser->setUsernameCanonical($username);
        $shopUser->setCustomer($customer);

        $this->objectManager->persist($shopUser);
        $this->objectManager->flush();

        $data = [
            'host' => $_SERVER['SERVER_NAME'],
            'user' => $shopUser,
            'password' => $password,
            'link' => '/confirmation-page/' . $hash,
            'email' => $username
        ];

        $this->sendMail($shopUser->getUsername(), $data, 'shop_user_register');

        return $shopUser;
    }

    /**
     * Resend confirmation email link
     *
     * @param ShopUser $shopUser
     */
    public function resendEmail(ShopUser $shopUser)
    {
        $hash = substr(md5(openssl_random_pseudo_bytes(20)), -20);
        $shopUser->setEmailVerificationToken($hash);
        $this->objectManager->persist($shopUser);
        $this->objectManager->flush();

        $data = [
            'host' => $_SERVER['SERVER_NAME'],
            'user' => $shopUser,
            'link' => '/confirmation-page/' . $hash,
            'email' => $shopUser->getUsername(),
            'password' => '*******'
        ];

        $this->sendMail($shopUser->getUsername(), $data, 'shop_user_resend_activation_link');
    }


    /**
     * Change password end send email
     *
     * @param ShopUser $shopUser
     * @param string $emailTemplate
     * @return string
     */
    public function changePassword(ShopUser $shopUser, $emailTemplate = 'shop_user_forgot_password')
    {
        $password = $this->randomPassword();
        $encodedPassword = (new MessageDigestPasswordEncoder())->encodePassword($password, $shopUser->getSalt());
        $shopUser->setPassword($encodedPassword);

        $this->objectManager->persist($shopUser);
        $this->objectManager->flush();
        $data = [
            'user' => $shopUser,
            'password' => $password,
        ];
        $this->sendMail($shopUser->getUsername(), $data, $emailTemplate);
    }

    /**
     * Generate random password
     *
     * @return string
     */
    public function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    /**
     *  Send password and confirm email link
     *
     * @param $email
     * @param array $data
     * @param $type
     */
    public function sendMail($email, array $data, $type)
    {
        if ($email) {
            $this->sender->send($type, [$email], ['data' => $data]);
        }
    }
}