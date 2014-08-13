<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Payer;
use PayPalRestApiClient\Model\Payment;

/**
 * The PaymentBuilder builds instances of PayPalRestApiClient\Model\Payment
 *
 * PaymentBuilder depends on 3 other builders: PayerBuilder,  TransactionsBuilder and LinkBuilder 
 */
class PaymentBuilder extends AbstractBuilder
{
    protected $payerBuilder;
    protected $linkBuilder;
    protected $transactionsBuilder;

    public function __construct()
    {
        $this->payerBuilder = new PayerBuilder();
        $this->transactionsBuilder = new TransactionsBuilder();
        $this->linkBuilder = new LinkBuilder();
    }

    public function setTransactionsBuilder($transactionsBuilder)
    {
        $this->transactionsBuilder = $transactionsBuilder;
    }

    public function setPayerBuilder($payerBuilder)
    {
        $this->payerBuilder = $payerBuilder;
    }

    public function setLinksBuilder($linkBuilder)
    {
        $this->linkBuilder = $linkBuilder;
    }    

    /**
     * Build an instance of PayPalRestApiClient\Model\Payment given an array
     *
     * @param array $data The array should contains the following keys: 
     * id, create_time, update_time, state, intent, payer, transactions, links
     * The "id" key value should not be empty.
     * 
     * @return PayPalRestApiClient\Model\Payment
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException If not all keys are set
     *
     * @see https://developer.paypal.com/docs/api/#payment-object
     */
    public function build(array $data)
    {
        $this->validateArrayKeys(
            array('id', 'create_time', 'update_time', 'state', 'intent', 'payer', 'transactions', 'links'),
            $data
        );


        if (empty($data['id'])) {
            throw new BuilderException('id is mandatory and should not be empty');
        }

        $links = array();
        foreach ($data['links'] as $link) {
            $links[] = $this->linkBuilder->build($link);
        }

        $payment = new Payment(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            $data['state'],
            $data['intent'],
            $this->payerBuilder->build($data['payer']),
            $this->transactionsBuilder->build($data['transactions']),
            $links
        );
        $payment->setPaypalData($data);

        return $payment;
    }
}
