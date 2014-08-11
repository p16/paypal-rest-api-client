<?php

namespace PayPalRestApiClient\Model;

/**
 * The AmountInterface is the interface that any object that represents a paypal amount should implement
 */
interface AmountInterface
{
    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return string
     */
    public function getTotal();

    /**
     * @return array
     */
    public function getDetails();
}