<?php

namespace PayPalRestApiClient\Model;

/**
 * The PayerInterface is the interface that any object that represents a paypal payer should implement
 */
interface PayerInterface
{
    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @return array
     */
    public function getFundingInstruments();

    /**
     * @return array
     */
    public function getInfo();
}