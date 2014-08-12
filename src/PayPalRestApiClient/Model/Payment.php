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
    protected $executeUrl;
    protected $approvalUrl;
    protected $captureUrl;

    protected function initUrls()
    {
        foreach ($this->getLinks() as $link) {
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

    /**
     * Returns the execute url that should be use to completed an approved paypal payment
     *
     * @return string
     */
    public function getExecuteUrl()
    {
        return $this->executeUrl;
    }

    /**
     * Returns the approval url that should be use to redirect the user to the paypal website
     *
     * @return string
     */
    public function getApprovalUrl()
    {
        return $this->approvalUrl;
    }

    /**
     * Returns the capture url that should be use to capture an authorized payment
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->captureUrl;
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