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
    public function __construct($paymentMethod, array $fundingInstruments = array(), array $info = array())
    {
        $this->paymentMethod = $paymentMethod;
        $this->fundingInstruments = $fundingInstruments;
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return array
     */
    public function getFundingInstruments()
    {
        return $this->fundingInstruments;
    }
}