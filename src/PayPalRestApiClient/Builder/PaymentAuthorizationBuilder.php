<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\PaymentAuthorization;
use PayPalRestApiClient\Model\Amount;

/**
 * The PaymentAuthorizationBuilder builds instances of PayPalRestApiClient\Model\PaymentAuthorization
 */
class PaymentAuthorizationBuilder
{
    /**
     * Build an instance of PayPalRestApiClient\Model\PaymentAuthorization given an array
     *
     * @param array $data The array should contains the following keys: 
     * id, create_time, update_time, state, intent, payer, transactions, links.
     * The "id" key value should not be empty.
     * 
     * @return PayPalRestApiClient\Model\PaymentAuthorization
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException If not all keys are set or when "id" is empty
     *
     * @see https://developer.paypal.com/docs/api/#authentication--headers
     */
    public function build(array $data)
    {
        $mandatoryKeys = array(
            'id', 'create_time', 'update_time', 'state', 'intent', 'payer', 'transactions', 'links',
        );
        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new BuilderException('Mandatory data missing for: '.implode(', ', $diff));
        }

        if (empty($data['id'])) {
            throw new BuilderException('id is mandatory and should not be empty');
        }

        $authorization = new PaymentAuthorization(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            $data['state'],
            $data['intent'],
            $data['payer'],
            $data['transactions'],
            $data['links']
        );
        $authorization->setPaypalData($data);

        return $authorization;
    }
}
