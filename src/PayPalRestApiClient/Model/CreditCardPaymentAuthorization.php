<?php

namespace PayPalRestApiClient\Model;

/**
 * The CreditCardPaymentAuthorization class represents a paypal payment authorization object created from a credit card autorization
 */
class CreditCardPaymentAuthorization extends AbstractPayment implements PaymentAuthorizationInterface
{
    protected $captureUrl;
    protected $authorization;

    protected function initUrls()
    {
        $links = $this->getLinks();
        $links = array_merge(
            $links,
            $this->getAuthorization()['links']
        );

        foreach ($links as $link) {
            switch ($link['rel']) {
                case 'capture':
                    $this->captureUrl = $link['href'];
                    return;
            }
        }
    }

    /**
     * Retruns the urls to call when capturing the authorized payments
     *
     * @return array
     */
    public function getCaptureUrl()
    {
        return $this->captureUrl;
    }

    /**
     * Retruns an authorization object
     *
     * @return array
     */
    public function getAuthorization()
    {
        return $this->transactions[0]['related_resources'][0]['authorization'];
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

        return new Amount(
            $this->transactions[0]['amount']['currency'],
            $this->transactions[0]['amount']['total'],
            $this->transactions[0]['amount']['details']
        );
    }
}