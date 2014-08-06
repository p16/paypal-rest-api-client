<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\PayerInterface;
use PayPalRestApiClient\Model\TransactionInterface;

class PaymentRequestBodyBuilder
{
    public function __construct($intent, $payer, $urls, $transactions)
    {
        $this->assertIntent($intent);
        $this->assertPayer($payer);
        $this->assertUrls($urls);
        $this->assertTransactions($transactions);
    }

    private function assertIntent($intent)
    {
        if ( ! in_array($intent, array('sale', 'authorize', 'order'))) {
            
            throw new BuilderException("intent is not valid: allowed value are 'sale', 'authorize', 'order'");
        }
    }

    private function assertPayer($payer)
    {
        if ($payer instanceof PayerInterface) {

            return;
        }

        if (($payer instanceof \ArrayAccess || is_array($payer)) && (isset($payer['payment_method']))) {

            return;
        }

        throw new BuilderException("payer is not valid: payment_method is missing");
    }

    private function assertUrls($urls)
    {
        if (
            ($urls instanceof \ArrayAccess || is_array($urls)) &&
            (isset($urls['return_url']) && isset($urls['cancel_url']))
        ) {

            return;
        }

        throw new BuilderException("urls are not valid: return_url and cancel_url are mandatory");
    }

    private function assertTransactions($transactions)
    {
        if (($transactions instanceof \ArrayAccess || is_array($transactions))) {

            foreach ($transactions as $transaction) {
                
                if ( ! $transaction instanceof TransactionInterface)
                {

                    throw new BuilderException("transactions is not valid: should contains only object implementing TransactionInterface");
                }
            }

            return;
        }

        throw new BuilderException("transactions is not valid: should be an array or implement \ArrayAccess interface");
    }
}
