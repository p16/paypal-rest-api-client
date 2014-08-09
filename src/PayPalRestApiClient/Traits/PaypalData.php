<?php

namespace PayPalRestApiClient\Traits;

trait PaypalData
{
    protected $paypalData;

    public function setPaypalData($data)
    {
        $this->paypalData = $data;
    }

    public function getPaypalData()
    {
        return $this->paypalData;
    }
}