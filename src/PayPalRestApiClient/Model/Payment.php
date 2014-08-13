<?php

namespace PayPalRestApiClient\Model;

/**
 * The Payment class represents a paypal payment object
 *
 * @see https://developer.paypal.com/docs/api/#payments
 * @see https://developer.paypal.com/docs/api/#payment-object
 */
class Payment extends AbstractPayment
{
    use \PayPalRestApiClient\Traits\LinkFinder;

    /**
     * Returns the execute url that should be use to completed an approved paypal payment
     *
     * @return string
     */
    public function getExecuteUrl()
    {
        $link = $this->findLink('execute');

        return $link->getHref();
    }

    /**
     * Returns the approval url that should be use to redirect the user to the paypal website
     *
     * @return string
     */
    public function getApprovalUrl()
    {
        $link = $this->findLink('approval_url');

        return $link->getHref();
    }
}