<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Authorization;
use PayPalRestApiClient\Model\Amount;

class AuthorizationBuilder
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

        if (empty($data['id'])) {
            throw new BuilderException('id is mandatory and should not be empty');
        }

        $capture = new Authorization(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            $data['state'],
            $data['intent'],
            $data['payer'],
            $data['transactions'],
            $data['links']
        );

        return $capture;
    }
}
