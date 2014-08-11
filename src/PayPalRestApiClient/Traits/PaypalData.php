<?php

namespace PayPalRestApiClient\Traits;

trait PaypalData
{
    protected $paypalData;

    public function setPaypalData(array $data)
    {
        $this->paypalData = $data;
    }

    public function getPaypalData()
    {
        return $this->paypalData;
    }
}