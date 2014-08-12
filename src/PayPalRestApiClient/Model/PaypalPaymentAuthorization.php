<?php

namespace PayPalRestApiClient\Model;

/**
 * The PaypalPaymentAuthorization class represents an authorization object with payment method "paypal"
 */
class PaypalPaymentAuthorization extends Payment implements PaymentAuthorizationInterface
{
    protected function initUrls()
    {
        $links = $this->links;
        if (isset($this->transactions[0]['related_resources'])) {
            $links = array_merge(
                $links,
                $this->transactions[0]['related_resources'][0]['authorization']['links']
            );
        }

        foreach ($links as $link) {
            switch ($link['rel']) {
                case 'approval_url':
                    $this->approvalUrl = $link['href'];
                    break;
                
                case 'execute':
                    $this->executeUrl = $link['href'];
                    break;

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
}