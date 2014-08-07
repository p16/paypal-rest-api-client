<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\PayerInterface;

class PayerBuilder
{
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
