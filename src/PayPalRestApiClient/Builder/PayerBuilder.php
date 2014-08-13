<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Payer;
use PayPalRestApiClient\Model\PayerInterface;

/**
 * The PayerBuilder builds arrays that represents a paypal payers or instances of Payer class from an array
 */
class PayerBuilder extends AbstractBuilder
{
    /**
     * Build an instance of PayPalRestApiClient\Model\Payer given an array
     *
     * @param array $data The array should contains the payment_method following
     * 
     * @return PayPalRestApiClient\Model\Payer
     *
     * @throws PayPalRestApiClient\Exception\BuilderException
     * 
     * @see https://developer.paypal.com/docs/api/#payer-object
     */
    public function build(array $data)
    {
        $this->validateArrayKeys(
            array('payment_method'),
            $data
        );

        if ( ! in_array($data['payment_method'], array('credit_card', 'paypal'))) {

            throw new BuilderException('Parameter payment_method not valid. Allowed values: "credit_card" and "paypal"');
        }

        $fundingInstruments = array();
        if (isset($data['funding_instruments'])) {
            $fundingInstruments = $data['funding_instruments'];
        }

        $info = array();
        if (isset($data['payer_info'])) {
            $info = $data['payer_info'];
        }

        return new Payer($data['payment_method'], $fundingInstruments, $info);
    }

    /**
     * Build an array that represents a paypal payer
     *
     * @param array|\ArrayAccess|PayPalRestApiClient\Model\PayerInterface $payer an array of values 
     * or an istance of a class implmenting PayPalRestApiClient\Model\PayerInterface or \ArrayAccess interface
     * 
     * @return array
     * 
     * @throws PayPalRestApiClient\Exception\BuilderException if the given input parameter is not an array or
     * it is not an object implementing PayPalRestApiClient\Model\PayerInterface or \ArrayAccess interface
     * 
     * @see https://developer.paypal.com/docs/api/#payer-object
     */
    public function buildArray($payer)
    {
        $payerData = array();
        if ($payer instanceof PayerInterface) {

            $payerData['payment_method'] = $payer->getPaymentMethod();

            if ($fundingInstruments = $payer->getFundingInstruments())
            {
                $payerData['funding_instruments'] = $fundingInstruments;
            }

            if ($info = $payer->getInfo())
            {
                $payerData['payer_info'] = $info;
            }

            return $payerData;
        }

        if (
            ($payer instanceof \ArrayAccess || is_array($payer)) &&
            isset($payer['payment_method'])
        ) {

            $payerData['payment_method'] = $payer['payment_method'];

            if (isset($payer['funding_instruments']))
            {
                $payerData['funding_instruments'] = $payer['funding_instruments'];
            }

            if (isset($payer['payer_info']))
            {
                $payerData['payer_info'] = $payer['payer_info'];
            }

            return $payerData;
        }

        throw new BuilderException('Payer is not valid');
    }
}
