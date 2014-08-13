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

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}