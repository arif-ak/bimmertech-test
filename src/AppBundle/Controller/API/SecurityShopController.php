<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Customer;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityShopController
 * @package AppBundle\Controller\API
 */
class SecurityShopController extends Controller implements SecurityShopInterface
{

    /**
     * Registration user
     *
     * @param Request $request
     * @return mixed|JsonResponse
     * @throws \Exception
     */
    public function registration(Request $request)
    {
        $email = $request->get('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new  JsonResponse(self::FORGOT_PASSWORD_INVALID_EMAIL_MESSAGE, 400);
        }

        $userService = $this->get('app.service.create_shop_user');

        if ($userService->createNewUser($email)) {
            return new  JsonResponse(self::REGISTRATION_SUCCESS_MESSAGE, 201);
        }
        return new  JsonResponse(self::REGISTRATION_USER_WITH_EMAIL_EXIST_MESSAGE, 409);
    }

    /**
     * Send forgot password email
     *
     * @param Request $request
     * @return mixed
     */
    public function forgotPassword(Request $request)
    {
        if (is_array($data = $this->checkEmail($request))) {
            return new  JsonResponse($data['message'], $data['code']);
        }
        $userService = $this->get('app.service.create_shop_user');
        $userService->changePassword($data);

        return new  JsonResponse(self::FORGOT_PASSWORD_SUCCESS_MESSAGE);
    }

    public function restoreAccount(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if (is_array($data = $this->checkEmail($request))) {
            return new  JsonResponse($data['message'], $data['code']);
        }

        /** @var ShopUser $shopUser */
        $shopUser = $data;

        if ($shopUser->getisClosedAccount() == true) {
            $shopUser->setIsClosedAccount(false);
            $shopUser->setEnabled(true);
            $userService = $this->get('app.service.create_shop_user');
            $userService->changePassword($shopUser, 'shop_user_restore_account');

            return new JsonResponse(['data' => "User account has been successfully restore"], 200);
        }

        return new JsonResponse(['error' => "User account was not close"], 400);
    }

    /**
     * Resend confirmation email
     *
     * @param Request $request
     * @return mixed
     */
    public function resendConfirmationEmail(Request $request)
    {
        if (is_array($data = $this->checkEmail($request))) {
            return new  JsonResponse($data['message'], $data['code']);
        }
        $userService = $this->get('app.service.create_shop_user');
        $userService->resendEmail($data);
        return new  JsonResponse(self::RESEND_CONFIRM_EMAIL, 201);
    }

    /**
     * Check was user authorized
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkLogin(Request $request)
    {

        /** @var ShopUser $shopUser */
        if (!$shopUser = $this->getUser()) {
            return new  JsonResponse(self::LOGIN_UNAUTHORIZED, 401);
        }

        $vinData = $request->getSession()->get('vincheck');
        /** @var Customer $customer */
        $customer = $shopUser->getCustomer();
        $data = [
            'customerId' => $customer->getId(),
            'username' => $shopUser->getUsername(),
            'email' => $shopUser->getEmail(),
            'avatar' => false,
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'phone' => $customer->getPhoneNumber(),
            'vinNumber' => $customer->getVinNumber(),
            'company' => $customer->getCompany(),
            'phoneNumber' => $customer->getPhoneNumber(),
            'birthday' => !$customer->getBirthday() ? NULL : $customer->getBirthday()->format('Y-m-d'),
            'vatNumber' => $customer->getVatNumber(),
            'gender' => $customer->getGender(),
            'currentVinNumber' => $vinData ? $vinData['vin'] : null,
        ];

        return new  JsonResponse($data, JsonResponse::HTTP_OK);
    }

    /**
     * Logout user action
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $this->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        return new JsonResponse('User logout');
    }

    /**
     * @param Request $request
     * @return mixed|void
     */
    public function login(Request $request)
    {
        // TODO: Implement login() method.
    }

    public function findEmail(Request $request)
    {
        $data = $this->checkEmail($request, $checkIsAccountClosed = true);

        if ($data instanceof ShopUser) {
            return new JsonResponse([
                'email' => $request->get('email'),
                'registered' => true,
                'closed_account' => false
            ]);
        }

        if (is_array($data) && $data['code'] == 403) {
            return new JsonResponse([
                'email' => $request->get('email'),
                'registered' => true,
                'closed_account' => true
            ]);
        }

        if (is_array($data) && $data['code'] == 404) {
            return new JsonResponse([
                'email' => $request->get('email'),
                'registered' => false,
                'closed_account' => false
            ]);
        }

        return new JsonResponse($data['message'], $data['code']);
    }

    /**
     * Find user by email
     *
     * @param Request $request
     * @param bool $checkIsAccountClosed
     * @return array|ShopUser
     */
    private function checkEmail(Request $request, $checkIsAccountClosed = false)
    {
        $email = $request->get('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'message' => self::FORGOT_PASSWORD_INVALID_EMAIL_MESSAGE,
                'code' => 400
            ];
        }

        $shopUserRepository = $this->get('sylius.repository.shop_user');
        $shopUser = $shopUserRepository->findOneByUsername($email);

        // check is account was closed
        /** @var ShopUser $shopUser */
        if ($shopUser && $checkIsAccountClosed) {
            if ($shopUser->getisClosedAccount()) {
                return [
                    'message' => self::LOGIN_ACCOUNT_CLOSED_MESSAGE,
                    'code' => 403
                ];
            }
        }

        if (!$shopUser) {
            return [
                'message' => self::FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE,
                'code' => 404
            ];
        }

        return $shopUser;
    }
}
