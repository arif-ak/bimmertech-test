<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use PayPal\Api\Sale;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\RefundRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\Payment;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PayPalService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $payPalApiMode;

    /**
     * PayPalService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        // id and secret of api application https://developer.paypal.com/developer/applications/
        $this->clientId = $container->getParameter("pay_pal_client_id");
        $this->clientSecret = $container->getParameter("pay_pal_client_secret");
        $this->payPalApiMode = $container->getParameter("pay_pal_api_mode");
    }

    /**
     * Helper method for getting an APIContext for all calls
     * @return ApiContext
     */
    public function getApiContext()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->clientId,
                $this->clientSecret
            )
        );

        $apiContext->setConfig(
            array(
                'mode' => $this->payPalApiMode,
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
            )
        );

        return $apiContext;
    }

    /**
     * @param null|string $transactionId
     * @return Sale
     */
    public function getSale(?string $transactionId)
    {
        $sale = new Sale();

        // transaction id from order payment
        $sale->setId($transactionId);

        return $sale;
    }

    /**
     * @param null|float $refundPrice
     * @return RefundRequest
     */
    public function refundRequest(?float $refundPrice)
    {
        $amt = new Amount();
        $amt->setCurrency('USD')
            ->setTotal($refundPrice);

        $refundRequest = new RefundRequest();
        $refundRequest->setAmount($amt);

        return $refundRequest;
    }

    public function getTransactionId(?Order $order)
    {
        $payment = $order->getPayments()->count() > 0 ? $order->getPayments()->filter(function (Payment $payment) {
            return $payment->getState() == PaymentInterface::STATE_COMPLETED;
        }) : null;

        $paymentDetails = $payment->count() > 0 ? $payment->first()->getDetails() : null;
        $transactionId = count($paymentDetails) > 0 ? $paymentDetails['TRANSACTIONID'] : false;

        return $transactionId;
    }
}
