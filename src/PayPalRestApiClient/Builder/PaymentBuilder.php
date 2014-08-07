<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Payer;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\Amount;
use PayPalRestApiClient\Model\Link;
use PayPalRestApiClient\Model\Payment;

class PaymentBuilder
{
    public function build(array $data)
    {
        $mandatoryKeys = array(
            'id', 'create_time', 'update_time', 'state', 'intent', 'payer', 'transactions', 'links',
        );
        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new BuilderException('Mandatory data missing for: '.implode(', ', $diff));
        }

        if ($data['state'] == 'approved' && $data['intent'] == 'authorize') {
            foreach ($data['transactions'] as $transaction) {
                foreach ($transaction['related_resources'] as $relatedResources) {
                    $data['links'] = array_merge(
                        $data['links'],
                        $relatedResources['authorization']['links']
                    );
                }
            }
        }

        $payment = new Payment(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            $data['state'],
            $data['intent'],
            $this->buildPayer($data['payer']),
            $this->buildTransactions($data['transactions']),
            $this->buildLinks($data['links'])
        );

        return $payment;
    }

    private function buildPayer($data)
    {
        $mandatoryKeys = array(
            'payment_method'
        );
        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new BuilderException('Mandatory data missing for: '.implode(', ', $diff));
        }

        $info = isset($data['payer_info']) ? $data['payer_info'] : null;
        $fundingInstruments = isset($data['funding_instruments']) ? $data['funding_instruments'] : null;

        return new Payer($data['payment_method'], $fundingInstruments, $info);
    }

    private function buildTransaction($data)
    {
        if ( ! isset($data['amount']))
        {
            throw new BuilderException('Mandatory data missing for: amount');
        }

        if ( ! isset($data['amount']['currency']) || ! isset($data['amount']['total']))
        {
            throw new BuilderException('Mandatory data missing for: amount currency or total');
        }

        $details = isset($data['amount']['details']) ? $data['amount']['details'] : null;
        $amount = new Amount($data['amount']['currency'], $data['amount']['total'], $details);

        $description = isset($data['description']) ? $data['description'] : null;
        $itemList = isset($data['item_list']) ? $data['item_list'] : array();
        $relatedResources = isset($data['related_resources']) ? $data['related_resources'] : array();

        return new Transaction($amount, $description, $itemList, $relatedResources);
    }

    private function buildTransactions(array $transactions)
    {
        $objs = array();
        foreach ($transactions as $transaction) {
            $objs[] = $this->buildTransaction($transaction);
        }

        return $objs;
    }

    private function buildLink($data)
    {
        $mandatoryKeys = array(
            'href', 'rel', 'method'
        );
        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new BuilderException('Mandatory data missing for: '.implode(', ', $diff));
        }

        return new Link($data['href'], $data['rel'], $data['method']);
    }

    private function buildLinks(array $links)
    {
        $objs = array();
        foreach ($links as $link) {
            $objs[] = $this->buildLink($link);
        }

        return $objs;
    }
}
