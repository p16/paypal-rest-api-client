<?php

namespace PayPalRestApiClient\Model;

interface AmountInterface
{
    public function getCurrency();

    public function getTotal();

    public function getDetails();
}