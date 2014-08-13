<?php

namespace PayPalRestApiClient\Model;

/**
 * The PaypalPaymentAuthorization class represents an authorization object with payment method "paypal"
 */
class PaypalPaymentAuthorization extends Payment implements PaymentAuthorizationInterface
{
    /**
     * If set, returns the first transaction amount object
     *
     * N.B.: At the moment, the PayPal REST API do not support multiple transactions
     *
     * @return PayPalRestApiClient\Model\Amount|null
     */
    public function getAmount()
    {
        if (count($this->transactions) <= 0) {
            return null;
        }

        return $this->transactions[0]->getAmount();
    }

    /**
     * Returns the capture url that should be use to capture an authorized payment
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->getAuthorization()->getCaptureUrl();
    }

    /**
     * Retruns an authorization object
     *
     * @return array
     */
    public function getAuthorization()
    {
        return $this->transactions[0]->getAuthorization();
    }
}