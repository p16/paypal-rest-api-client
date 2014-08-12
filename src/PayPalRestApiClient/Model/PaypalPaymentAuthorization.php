<?php

namespace PayPalRestApiClient\Model;

/**
 * The PaypalPaymentAuthorization class represents an authorization object with payment method "paypal"
 */
class PaypalPaymentAuthorization extends Payment implements PaymentAuthorizationInterface
{
    protected function initUrls()
    {
        parent::initUrls();

        $links = array();
        if ($authorization = $this->getAuthorization()) {
            $links = $authorization['links'];
        }

        foreach ($links as $link) {
            switch ($link['rel']) {
                case 'capture':
                    $this->captureUrl = $link['href'];
                    break;
            }
        }
    }

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

    /**
     * Retruns an authorization object
     *
     * @return array
     */
    public function getAuthorization()
    {
        if (isset($this->transactions[0]['related_resources'])) {
            return $this->transactions[0]['related_resources'][0]['authorization'];
        }
    }
}