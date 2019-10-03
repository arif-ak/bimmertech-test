<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ForgotPasswordType;
use AppBundle\Form\Type\RegistrationType;
use Sylius\Bundle\UserBundle\Form\Type\UserLoginType;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class SecurityShopController extends Controller
{
    const REGISTRATION_INVALID_EMAIL_CODE = "_registration.email.not_valid";
    const REGISTRATION_INVALID_EMAIL_ERROR_MESSAGE = "Please enter a valid email address";

    const REGISTRATION_REPEATED_TYPE_CODE = "_registration.email.repeated_type";
    const REGISTRATION_REPEATED_TYPE_ERROR_MESSAGE = "The emails don't match. Please check and try again";

    const REGISTRATION_USER_WITH_EMAIL_EXIST_CODE = "_registration.user_exist";
    const REGISTRATION_USER_WITH_EMAIL_EXIST_MESSAGE = "The email address is already registered in the system.
     Please log in to your existing account";

    const REGISTRATION_REQUIRED_CODE = "_forgot_password.email.required";
    const REGISTRATION_REQUIRED_MESSAGE = "This fields are required";

    const REGISTRATION_SUCCESS_CODE = "_registration.success";
    const REGISTRATION_SUCCESS_MESSAGE = "Check your inbox for an email confirmation link and a
     generated password to access your account.";


    const LOGIN_INVALID_CREDENTIAL_CODE = "Bad credentials.";
    const LOGIN_INVALID_CREDENTIAL_MESSAGE = "Please, check you email/password and type correct.";

    const LOGIN_DISABLE_ACCOUNT_CODE = "User account is disabled.";
    const LOGIN_DISABLE_ACCOUNT_MESSAGE = "You didn't conform your email. Please check your inbox and click the
     confirmation link or ";


    const FORGOT_PASSWORD_INVALID_EMAIL_CODE = "_forgot_password.email.not_valid";
    const FORGOT_PASSWORD_INVALID_EMAIL_MESSAGE = "Please enter a valid email address";

    const FORGOT_PASSWORD_REQUIRED_CODE = "_forgot_password.email.required";
    const FORGOT_PASSWORD_REQUIRED_MESSAGE = "This field is required";

    const FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_CODE = "_forgot.user_not_exist";
    const FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE = "The email address isn't already registered in the system";

    const RESEND_PASSWORD_INVALID_EMAIL_CODE = "_forgot_password.email.not_valid";
    const RESEND_PASSWORD_INVALID_EMAIL_MESSAGE = "Please enter a valid email address";

    const RESEND_PASSWORD_REQUIRED_CODE = "_forgot_password.email.required";
    const RESEND_PASSWORD_REQUIRED_MESSAGE = "This field is required";

    const RESEND_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_CODE = "_forgot.user_not_exist";
    const RESEND_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE = "The email address isn't already registered in the system";

    /**
     * Login form action.
     */
    public function login(Request $request)
    {
        $session = $request->getSession();
        $securityContext = $this->container->get('security.authorization_checker');

        $showLoginForm = false;
        $showRegistrationForm = false;
        $showMessage = false;
        $showLinkAccount = false;
        $showResendLinkForm = false;
        $showForgotPasswordForm = false;

        $successMessage = "";
        $errorMessage = "";

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // login form
        $formType = $options['form'] ?? UserLoginType::class;
        $form = $this->get('form.factory')->createNamed('', $formType);

        // registration form
        $formRegistration = $options['form'] ?? RegistrationType::class;
        $formRegistrationType = $this->get('form.factory')->createNamed('', $formRegistration);

        if ($request->getSession()->has(self::REGISTRATION_SUCCESS_CODE)) {
            $showMessage = true;
            $successMessage = self::REGISTRATION_SUCCESS_MESSAGE;

            $this->removeSession($session, self::REGISTRATION_SUCCESS_CODE);
        }


        if ($error) {
            if ($error->getMessage() == self::LOGIN_INVALID_CREDENTIAL_CODE) {
                $errorMessage = self::LOGIN_INVALID_CREDENTIAL_MESSAGE;
            }

            if ($error->getMessage() == self::LOGIN_DISABLE_ACCOUNT_CODE) {
                $errorMessage = "disabled";
            }

            $showLoginForm = true;
        }

        if ($request->getSession()->has(self::REGISTRATION_REPEATED_TYPE_CODE)) {
            $errorMessage = self::REGISTRATION_REPEATED_TYPE_ERROR_MESSAGE;
            $showRegistrationForm = true;

            $this->removeSession($session, self::REGISTRATION_REPEATED_TYPE_CODE);
        }

        if ($request->getSession()->has(self::REGISTRATION_USER_WITH_EMAIL_EXIST_CODE)) {
            $errorMessage = self::REGISTRATION_USER_WITH_EMAIL_EXIST_MESSAGE;
            $showRegistrationForm = true;

            $this->removeSession($session, self::REGISTRATION_USER_WITH_EMAIL_EXIST_CODE);
        }

        if ($request->getSession()->has(self::REGISTRATION_INVALID_EMAIL_CODE)) {
            $errorMessage = self::REGISTRATION_INVALID_EMAIL_ERROR_MESSAGE;
            $showRegistrationForm = true;

            $this->removeSession($session, self::REGISTRATION_INVALID_EMAIL_CODE);
        }

        if ($request->getSession()->has(self::REGISTRATION_REQUIRED_CODE)) {
            $errorMessage = self::REGISTRATION_REQUIRED_MESSAGE;
            $showRegistrationForm = true;

            $this->removeSession($session, self::REGISTRATION_REQUIRED_CODE);
        }

        // Start Forgot password
        if ($request->getSession()->has(self::FORGOT_PASSWORD_INVALID_EMAIL_CODE)) {
            $errorMessage = self::FORGOT_PASSWORD_INVALID_EMAIL_MESSAGE;
            $showForgotPasswordForm = true;

            $this->removeSession($session, self::FORGOT_PASSWORD_INVALID_EMAIL_CODE);
        }

        if ($request->getSession()->has(self::FORGOT_PASSWORD_REQUIRED_CODE)) {
            $errorMessage = self::FORGOT_PASSWORD_REQUIRED_MESSAGE;
            $showForgotPasswordForm = true;

            $this->removeSession($session, self::FORGOT_PASSWORD_REQUIRED_CODE);
        }

        if ($request->getSession()->has(self::FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_CODE)) {
            $errorMessage = self::FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE;
            $showForgotPasswordForm = true;

            $this->removeSession($session, self::FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_CODE);
        }
        // End Forgot password

        // Start Resend
        if ($request->getSession()->has(self::RESEND_PASSWORD_INVALID_EMAIL_CODE)) {
            $errorMessage = self::RESEND_PASSWORD_INVALID_EMAIL_MESSAGE;
            $showResendLinkForm = true;

            $this->removeSession($session, self::RESEND_PASSWORD_INVALID_EMAIL_CODE);
        }

        if ($request->getSession()->has(self::RESEND_PASSWORD_REQUIRED_CODE)) {
            $errorMessage = self::RESEND_PASSWORD_REQUIRED_MESSAGE;
            $showResendLinkForm = true;

            $this->removeSession($session, self::RESEND_PASSWORD_REQUIRED_CODE);
        }

        if ($request->getSession()->has(self::RESEND_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_CODE)) {
            $errorMessage = self::RESEND_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE;
            $showResendLinkForm = true;

            $this->removeSession($session, self::RESEND_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_CODE);
        }
        // End Resend
//
//        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY') && !$successMessage && !$errorMessage) {
//            $showLinkAccount = true;
//        }
//
//        if (!$securityContext->isGranted('IS_AUTHENTICATED_FULLY') && !$errorMessage && !$successMessage) {
//            $showLoginForm = true;
//        }


        $showLinkAccount = true;
        $showLoginForm = true;


        {
            return $this->render(':ShopLogin:login.html.twig', [
                'form' => $form->createView(),
                'formRegistration' => $formRegistrationType->createView(),
                'showLoginForm' => $showLoginForm,
                'showRegistrationForm' => $showRegistrationForm,
                'showMessage' => $showMessage,
                'showLinkAccount' => $showLinkAccount,
                'showForgotPasswordForm' => $showForgotPasswordForm,
                'showResendLinkForm' => $showResendLinkForm,
                'successMessage' => $successMessage,
                'errorMessage' => $errorMessage,
            ]);
        }
    }

    public function registration(Request $request)
    {
        $formRegistration = $options['form'] ?? RegistrationType::class;
        $formRegistrationType = $this->get('form.factory')->createNamed('', $formRegistration);
        $formRegistrationType->handleRequest($request);

        if ($request->isMethod("POST") && $formRegistrationType->isValid()) {
            $data = $request->request->all();
            $email = $data['email']['first'];
            $emailSecond = $data['email']['second'];

            // email validation
            $emailConstraint = new EmailConstraint;
            $emailConstraint->message = self::REGISTRATION_INVALID_EMAIL_ERROR_MESSAGE;
            $errors = $this->container->get('validator')->validate(
                $email,
                $emailConstraint
            );

            if (!$email || !$emailSecond) {
                $request->getSession()->set(self::REGISTRATION_REQUIRED_CODE,
                    self::REGISTRATION_REQUIRED_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            if (count($errors) > 0) {
                $request->getSession()->set(self::REGISTRATION_INVALID_EMAIL_CODE,
                    self::REGISTRATION_INVALID_EMAIL_ERROR_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            $shopUserRepository = $this->container->get('sylius.repository.shop_user');
            $shopUser = $shopUserRepository->findBy(['username' => $email]);
            if ($shopUser) {
                $request->getSession()->set(self::REGISTRATION_USER_WITH_EMAIL_EXIST_CODE,
                    self::REGISTRATION_USER_WITH_EMAIL_EXIST_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            $password = $this->randomPassword();
            $hash = substr(md5(openssl_random_pseudo_bytes(20)), -20);

            $customerRepository = $this->container->get('sylius.repository.customer');
            /** @var CustomerInterface $customer */
            $customer = $customerRepository->findOneBy(['email' => $email]);
            if (!$customer) {
                /** @var CustomerInterface $customer */
                $customer = $this->container->get('sylius.factory.customer')->createNew();
                $customer->setEmail($email);
                $customerRepository->add($customer);
            }

            /** @var ShopUserInterface $shopUser */
            $shopUser = $this->container->get('sylius.factory.shop_user')->createNew();
            $shopUser->setCustomer($customer);
            $shopUser->setEmail($email);
            $shopUser->setUsername($email);
            $shopUser->setUsernameCanonical($email);
            $shopUser->setEmailCanonical($email);
            $shopUser->setPlainPassword($password);
            $shopUser->setEmailVerificationToken($hash);

            $shopUserRepository->add($shopUser);

            $data = [
                'user' => $shopUser,
                'password' => $password,
                'link' => $_SERVER['HTTP_ORIGIN'] . '/verify/' . $hash,
            ];

            $this->sendRegisterMail($email, $data);

            $request->getSession()->set(self::REGISTRATION_SUCCESS_CODE,
                self::REGISTRATION_SUCCESS_MESSAGE);

            return $this->redirectToRoute('sylius_shop_homepage');
        }

        $request->getSession()->set(self::REGISTRATION_REPEATED_TYPE_CODE,
            self::REGISTRATION_REPEATED_TYPE_ERROR_MESSAGE);

        return $this->redirectToRoute('sylius_shop_homepage');
    }

    public function forgotPassword(Request $request)
    {
        $formForgotPassword = $options['form'] ?? ForgotPasswordType::class;
        $formForgotPasswordType = $this->get('form.factory')->createNamed('', $formForgotPassword);
        $formForgotPasswordType->handleRequest($request);

        if ($request->isMethod("POST") && $formForgotPasswordType->isValid()) {
            $data = $request->request->all();
            $email = $data['email'];

            if (empty($email)) {
                $request->getSession()->set(self::FORGOT_PASSWORD_REQUIRED_CODE,
                    self::FORGOT_PASSWORD_REQUIRED_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            // email validation
            $emailConstraint = new EmailConstraint;
            $emailConstraint->message = self::FORGOT_PASSWORD_REQUIRED_MESSAGE;
            $errors = $this->container->get('validator')->validate(
                $email,
                $emailConstraint
            );

            if (count($errors) > 0) {
                $request->getSession()->set(self::FORGOT_PASSWORD_INVALID_EMAIL_CODE,
                    self::FORGOT_PASSWORD_INVALID_EMAIL_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            $shopUserRepository = $this->container->get('sylius.repository.shop_user');
            /** @var ShopUserInterface $shopUser */
            $shopUser = $shopUserRepository->findOneBy(['username' => $email]);
            if (!$shopUser) {
                $request->getSession()->set(self::FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_CODE,
                    self::FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            $password = $this->randomPassword();
            $encodedPassword = (new MessageDigestPasswordEncoder())->encodePassword($password, $shopUser->getSalt());

            $shopUser->setPassword($encodedPassword);

            $em = $this->container->get('doctrine.orm.entity_manager');
            $em->flush($shopUser);

            $data = [
                'user' => $shopUser,
                'password' => $password,
            ];

            $this->sendRegisterMail($email, $data, 'shop_user_forgot_password');

            return $this->redirectToRoute('sylius_shop_homepage');
        } else {
            return $this->redirectToRoute('sylius_shop_homepage');
        }
    }

    public function resendLink(Request $request)
    {
        $formForgotPassword = $options['form'] ?? ForgotPasswordType::class;
        $formForgotPasswordType = $this->get('form.factory')->createNamed('', $formForgotPassword);
        $formForgotPasswordType->handleRequest($request);

        if ($request->isMethod("POST") && $formForgotPasswordType->isValid()) {
            $data = $request->request->all();
            $email = $data['email'];

            if (empty($email)) {
                $request->getSession()->set(self::RESEND_PASSWORD_REQUIRED_CODE,
                    self::RESEND_PASSWORD_REQUIRED_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            // email validation
            $emailConstraint = new EmailConstraint;
            $emailConstraint->message = self::RESEND_PASSWORD_REQUIRED_MESSAGE;
            $errors = $this->container->get('validator')->validate(
                $email,
                $emailConstraint
            );

            if (count($errors) > 0) {
                $request->getSession()->set(self::RESEND_PASSWORD_INVALID_EMAIL_CODE,
                    self::RESEND_PASSWORD_INVALID_EMAIL_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            $shopUserRepository = $this->container->get('sylius.repository.shop_user');
            /** @var ShopUserInterface $shopUser */
            $shopUser = $shopUserRepository->findOneBy(['username' => $email]);
            if (!$shopUser) {
                $request->getSession()->set(self::RESEND_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_CODE,
                    self::RESEND_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE);

                return $this->redirectToRoute('sylius_shop_homepage');
            }

            $hash = substr(md5(openssl_random_pseudo_bytes(20)), -20);
            $shopUser->setEmailVerificationToken($hash);

            $em = $this->container->get('doctrine.orm.entity_manager');
            $em->flush($shopUser);

            $data = [
                'user' => $shopUser,
                'link' => $_SERVER['HTTP_ORIGIN'] . '/verify/' . $hash,
            ];

            $this->sendRegisterMail($email, $data, 'shop_user_resend_activation_link');

            return $this->redirectToRoute('sylius_shop_homepage');
        } else {
            return $this->redirectToRoute('sylius_shop_homepage');
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form): array
    {
        // Errors container
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    foreach ($childErrors as $errorMessage) {
                        $errors[] = [
                            'source' => $childForm->getName(),
                            'details' => $errorMessage,
                        ];
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Generate random password
     *
     * @return string
     */
    public function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = [];
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
     * @param       $email
     * @param array $data
     * @param null $chooseService
     */
    private function sendRegisterMail($email, array $data, $chooseService = null)
    {
        $sender = $this->container->get('sylius.email_sender');

        if ($chooseService) {
            $sender->send($chooseService, [$email], ['data' => $data]);
        } else {
            $sender->send('shop_user_register', [$email], ['data' => $data]);
        }
    }

    public function removeSession($session, $key)
    {
        $session->remove($key);
    }
}
