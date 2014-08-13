<?php

namespace PayPalRestApiClient\Model;

/**
 * The CreditCardPaymentAuthorization class represents a paypal payment authorization object created from a credit card autorization
 */
class CreditCardPaymentAuthorization extends AbstractPayment implements PaymentAuthorizationInterface
{
    /**
     * Retruns the urls to call when capturing the authorized payments
     *
     * @return array
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
}