<?php

namespace PayPalRestApiClient\Model;

interface PayerInterface
{
    public function getPaymentMethod();

    public function getFundingInstruments();

    public function getInfo();
}