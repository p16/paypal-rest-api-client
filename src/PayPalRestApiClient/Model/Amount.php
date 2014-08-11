<?php

namespace PayPalRestApiClient\Model;

/**
 * The Amount class represents a paypal amount object
 */
class Amount implements AmountInterface
{
    protected $currency;
    protected $total;
    protected $details;

    /**
     * Construct 
     *
     * @param string $currency not null
     * @param string $total not null
     * @param array $details default is an empty array
     *
     * @see https://developer.paypal.com/docs/api/#amount-object
     */
    public function __construct($currency, $total, array $details = array())
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