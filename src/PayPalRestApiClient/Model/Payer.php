<?php

namespace PayPalRestApiClient\Model;

class Payer
{
    protected $paymentMethod;
    protected $info;
    protected $fundingInstruments;

    /**
     * @see https://developer.paypal.com/docs/api/#payer-object
     */
    public function __construct($paymentMethod, $info = null, $fundingInstruments = null)
    {
        $this->paymentMethod = $paymentMethod;
        $this->info = $info;
        $this->fundingInstruments = $fundingInstruments;
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