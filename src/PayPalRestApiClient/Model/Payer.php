<?php

namespace PayPalRestApiClient\Model;

/**
 * The Payer class represents a paypal payer object
 */
class Payer implements PayerInterface
{
    protected $paymentMethod;
    protected $info;
    protected $fundingInstruments;

    /**
     * Construct 
     *
     * @param string $paymentMethod not null
     * @param array $fundingInstruments default null
     * @param array $info default null
     *
     * @see https://developer.paypal.com/docs/api/#payer-object
     */
    public function __construct($paymentMethod, array $fundingInstruments = null, array $info = null)
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