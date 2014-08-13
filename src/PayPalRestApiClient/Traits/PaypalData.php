<?php

namespace PayPalRestApiClient\Traits;

/**
 * PaypalData trait defines paypal data property and its methods
 */
trait PaypalData
{
    protected $paypalData;

    /**
     * @param array $data
     */
    public function setPaypalData(array $data)
    {
        $this->paypalData = $data;
    }

    /**
     * @return array
     */
    public function getPaypalData()
    {
        return $this->paypalData;
    }
}