<?php

namespace PayPalRestApiClient\Model;

class Amount implements AmountInterface
{
    protected $currency;
    protected $total;
    protected $details;

    /**
     * @see https://developer.paypal.com/docs/api/#amount-object
     */
    public function __construct($currency, $total, $details = array())
    {
        $this->currency = $currency;
        $this->total = $total;
        $this->details = $details;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getDetails()
    {
        return $this->details;
    }
}