<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Payer;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\Amount;
use PayPalRestApiClient\Model\Link;
use PayPalRestApiClient\Model\Payment;

/**
 * The CaptureBuilder builds instances of PayPalRestApiClient\Model\Payment
 */
class PaymentBuilder
{
    /**
     * Build an instance of PayPalRestApiClient\Model\Payment given an array
     *
     * @param array $data The array should contains the following keys: 
     * id, create_time, update_time, state, intent, payer, transactions, links
     * 
     * @return PayPalRestApiClient\Model\Payment
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException If not all keys are set
     *
     * @see https://developer.paypal.com/docs/api/#payment-object
     */
    public function build(array $data)
    {
        $mandatoryKeys = array(
            'id', 'create_time', 'update_time', 'state', 'intent', 'payer', 'transactions', 'links',
        );
        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new BuilderException('Mandatory data missing for payment: '.implode(', ', $diff));
        }

        $payment = new Payment(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            $data['state'],
            $data['intent'],
            $data['payer'],
            $data['transactions'],
            $data['links']
        );
        $payment->setPaypalData($data);

        return $payment;
    }
}
