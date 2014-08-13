<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Amount;

/**
 * The AmountBuilder builds instances of PayPalRestApiClient\Model\Amount
 */
class AmountBuilder extends AbstractBuilder
{
    /**
     * Build an instance of PayPalRestApiClient\Model\Amount given an array
     *
     * @param array $data 
     * 
     * @return PayPalRestApiClient\Model\Amount
     *
     * @throws PayPalRestApiClient\Exception\BuilderException
     * 
     * @see https://developer.paypal.com/docs/api/#authentication--headers
     */
    public function build(array $data)
    {
        $this->validateArrayKeys(
            array('currency', 'total'),
            $data
        );

        $details = array();
        if (isset($data['details'])) {
            $details = $data['details'];
        }

        $amount = new Amount(
            $data['currency'],
            $data['total'],
            $details
        );

        return $amount;
    }
}
