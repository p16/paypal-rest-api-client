<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\PayerInterface;
use PayPalRestApiClient\Model\TransactionInterface;

class PaymentRequestBodyBuilder
{
    protected $intent;
    protected $payer;
    protected $urls;
    protected $transactions;

    public function __construct($intent, $payer, $urls, $transactions)
    {
        $this->assertIntent($intent);
        $this->assertPayer($payer);
        $this->assertUrls($urls);
        $this->assertTransactions($transactions);

        $this->intent = $intent;
        $this->payer = $payer;
        $this->urls = $urls;
        $this->transactions = $transactions;
    }

    public function build()
    {
        $requestBody = array();

        $requestBody['intent'] = $this->intent;
        $requestBody['payer'] = $this->buildPayerArray();
        $requestBody['redirect_urls'] = $this->buildUrlsArray();
        $requestBody['transactions'] = $this->buildTransactionsArray();

        return $requestBody;
    }

    private function buildUrlsArray()
    {
        return $this->urls;
    }

    private function buildTransactionsArray()
    {
        $transactions = array();
        foreach ($this->transactions as $transaction) {
            if ($transaction instanceof TransactionInterface) {
                $data = array(
                    'amount' => array(
                        'total' => $transaction->getAmount()->getTotal(),
                        'currency' => $transaction->getAmount()->getCurrency(),
                    ),
                    'description' => $transaction->getDescription()
                );

                if ($itemList = $transaction->getItemList())
                {
                    $data['item_list'] = $itemList;
                }

                $transactions[] = $data;
            }
            else {
                $transactions[] = $transaction;
            }
        }

        return $transactions;
    }

    private function buildPayerArray()
    {
        $payer = array();
        if ($this->payer instanceof PayerInterface) {

            $payer['payment_method'] = $this->payer->getPaymentMethod();

            if ($fundingInstruments = $this->payer->getFundingInstruments())
            {
                $payer['funding_instruments'] = $fundingInstruments;
            }

            if ($info = $this->payer->getInfo())
            {
                $payer['payer_info'] = $info;
            }

            return $payer;
        }

        if ($this->payer instanceof \ArrayAccess || is_array($this->payer)) {

            $payer['payment_method'] = $this->payer['payment_method'];

            if (isset($this->payer['funding_instruments']))
            {
                $payer['funding_instruments'] = $this->payer['funding_instruments'];
            }

            if (isset($this->payer['payer_info']))
            {
                $payer['payer_info'] = $this->payer['payer_info'];
            }

            return $payer;
        }

        throw new BuilderException('Payer is not valid');
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
                
                if ( ! $transaction instanceof TransactionInterface &&
                     ! ($transaction instanceof \ArrayAccess || is_array($transaction))
                ) {

                    throw new BuilderException("transactions is not valid: should contains only object implementing TransactionInterface");
                }
            }

            return;
        }

        throw new BuilderException("transactions is not valid: should be an array or implement \ArrayAccess interface");
    }
}
