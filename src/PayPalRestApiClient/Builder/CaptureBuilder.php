<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Capture;
use PayPalRestApiClient\Model\Amount;

/**
 * The CaptureBuilder builds instances of PayPalRestApiClient\Model\Capture
 */
class CaptureBuilder
{
    /**
     * Build an instance of PayPalRestApiClient\Model\Capture given an array
     *
     * @param array $data The array should contains the following keys: 
     * id, create_time, update_time, amount, is_final_capture, state, parent_payment, links.
     * 
     * @return PayPalRestApiClient\Model\Capture
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException If not all keys are set
     *
     * @see https://developer.paypal.com/docs/api/#capture-object
     */
    public function build(array $data)
    {
        $mandatoryKeys = array(
            'id', 'create_time', 'update_time', 'amount', 'is_final_capture', 'state', 'parent_payment', 'links',
        );
        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new BuilderException('Mandatory data missing for: '.implode(', ', $diff));
        }

        $capture = new Capture(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            new Amount($data['amount']['currency'], $data['amount']['total']),
            $data['is_final_capture'],
            $data['state'],
            $data['parent_payment'],
            $data['links']
        );
        $capture->setPaypalData($data);

        return $capture;
    }
}
