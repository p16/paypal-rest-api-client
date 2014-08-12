<?php

namespace PayPalRestApiClient\Model;

/**
 * The PaymentAuthorizationInterface is the interface that any object that represents a payment authorization should implement
 */
interface PaymentAuthorizationInterface
{
    /**
     * @return string
     */
    public function getCaptureUrl();

    /**
     * @return array
     */
    public function getAmount();

    /**
     * @return array
     */
    public function getAuthorization();
}