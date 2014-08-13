<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\PaypalPaymentAuthorization;
use PayPalRestApiClient\Model\CreditCardPaymentAuthorization;

/**
 * The PaymentAuthorizationBuilder builds instances
 * of PayPalRestApiClient\Model\PaypalPaymentAuthorization
 * or PayPalRestApiClient\Model\CreditCardPaymentAuthorization 
 * based on the payer "payment_method"
 *
 * PaymentAuthorizationBuilder depends on 3 other builders: PayerBuilder,  TransactionsBuilder and LinkBuilder
 */
class PaymentAuthorizationBuilder extends AbstractBuilder
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
     * Build an instance of PayPalRestApiClient\Model\PaymentAuthorization given an array
     *
     * @param array $data The array should contains the following keys: 
     * id, create_time, update_time, state, intent, payer, transactions, links.
     * The "id" key value should not be empty.
     * 
     * @return PayPalRestApiClient\Model\PaypalPaymentAuthorization|PayPalRestApiClient\Model\CreditCardPaymentAuthorization
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException If not all keys are set or when "id" is empty
     *
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/#authorize-the-payment
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

        $payer = $this->payerBuilder->build($data['payer']);
        $transactions = $this->transactionsBuilder->build($data['transactions']);

        $links = array();
        foreach ($data['links'] as $link) {
            $links[] = $this->linkBuilder->build($link);
        }

        if ($data['payer']['payment_method'] === 'paypal') {
            $authorization = new PaypalPaymentAuthorization(
                $data['id'],
                $data['create_time'],
                $data['update_time'],
                $data['state'],
                $data['intent'],
                $payer,
                $transactions,
                $links
            );
            $authorization->setPaypalData($data);

            return $authorization;
        }

        $authorization = new CreditCardPaymentAuthorization(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            $data['state'],
            $data['intent'],
            $payer,
            $transactions,
            $links
        );
        $authorization->setPaypalData($data);

        return $authorization;
    }
}
