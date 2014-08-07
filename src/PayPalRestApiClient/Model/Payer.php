<?php

namespace PayPalRestApiClient\Model;

class Payer implements PayerInterface
{
    protected $paymentMethod;
    protected $info;
    protected $fundingInstruments;

    /**
     * @see https://developer.paypal.com/docs/api/#payer-object
     */
    public function __construct($paymentMethod, $fundingInstruments = null, $info = null)
    {
        $this->paymentMethod = $paymentMethod;
        $this->fundingInstruments = $fundingInstruments;
        $this->info = $info;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getFundingInstruments()
    {
        return $this->fundingInstruments;
    }
}