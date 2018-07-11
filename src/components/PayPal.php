<?php

namespace mate\yii\components;

use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use yii\base\Component;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PayPal extends Component
{

    public $client_id;
    public $client_secret;
    public $log_file;
    public $mode;

    public $feePercent = 1.9;
    public $feeFixed = 0.35;
    public $currency = 'EUR';

    private $apiContext;

    public function init()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential($this->client_id, $this->client_secret)
        );

        if (YII_ENV_DEV && $this->log_file) {
            $this->apiContext->setConfig([
                'log.LogEnabled' => true,
                'log.FileName' => $this->log_file,
                'log.LogLevel' => 'DEBUG'
            ]);
        }
        if ($this->mode == 'live') {
            $this->apiContext->setConfig([
                'mode' => 'live',
            ]);
        }
    }

    public function createTransaction($total)
    {
        $amount = new Amount();
        $amount->setTotal($total);
        $amount->setCurrency($this->currency);

        $transaction = new Transaction();
        $transaction->setAmount($amount);

        return $transaction;
    }

    public function createPayment($paymentMethod = 'paypal', $intend = 'sale')
    {
        $payer = new Payer();
        $payer->setPaymentMethod($paymentMethod);

        $payment = new Payment();
        $payment->setIntent($intend);
        $payment->setPayer($payer);

        return $payment;
    }

    /**
     * @return mixed
     */
    public function getApiContext()
    {
        return $this->apiContext;
    }

}