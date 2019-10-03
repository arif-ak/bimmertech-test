<?php

namespace AppBundle\Controller\API;


use Symfony\Component\HttpFoundation\Request;

/**
 * Interface SecurityShopInterface
 * @package AppBundle\Controller\API
 */
interface SecurityShopInterface
{
    const REGISTRATION_INVALID_EMAIL_ERROR_MESSAGE = "* Please enter a valid email address";
    const REGISTRATION_REPEATED_TYPE_ERROR_MESSAGE = "* The emails don't match. Please check and try again";
    const REGISTRATION_USER_WITH_EMAIL_EXIST_MESSAGE = "The email address is already registered in the system. Please log in to your existing account";
    const REGISTRATION_REQUIRED_MESSAGE = "* This fields are required";
    const REGISTRATION_SUCCESS_MESSAGE = "Check your inbox for an email confirmation link and a generated password to access your account.";

    const LOGIN_INVALID_CREDENTIAL_MESSAGE = "Please, check you email/password and type correct.";
    const LOGIN_DISABLE_ACCOUNT_MESSAGE = "* You didn't conform your email. Please check your inbox and click the confirmation link or ";
    const LOGIN_UNAUTHORIZED = "User unauthorized ";

    const FORGOT_PASSWORD_INVALID_EMAIL_MESSAGE = "* Please enter a valid email address";
    const FORGOT_PASSWORD_REQUIRED_MESSAGE = "* This field is required";
    const FORGOT_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE = "* The email address isn't already registered in the system";
    const FORGOT_PASSWORD_SUCCESS_MESSAGE = "Check the Inbox to receive an email password to access your account.";

    const RESEND_PASSWORD_INVALID_EMAIL_MESSAGE = "* Please enter a valid email address";
    const RESEND_PASSWORD_REQUIRED_MESSAGE = "* This field is required";
    const RESEND_PASSWORD_USER_WITH_EMAIL_NOT_EXIST_MESSAGE = "* The email address isn't already registered in the system";
    const RESEND_CONFIRM_EMAIL = "Check your inbox for an email confirmation link";

    const LOGIN_ACCOUNT_CLOSED_MESSAGE = 'This email is already registered. To activate the account, please click on the button and password will be sent to your inbox';

    /**
     * Login user
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request);

    /**
     * Registration user
     *
     * @param Request $request
     * @return mixed
     */
    public function registration(Request $request);

    /**
     * Send forgot password email
     *
     * @param Request $request
     * @return mixed
     */
    public function forgotPassword(Request $request);

    /**
     * Resend confirmation email
     *
     * @param Request $request
     * @return mixed
     */
    public function ResendConfirmationEmail(Request $request);

}