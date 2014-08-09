<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\AccessToken;

class AccessTokenBuilder
{
    public function build(array $data)
    {
        $mandatoryKeys = array(
            'scope', 'access_token', 'token_type', 'app_id', 'expires_in',
        );
        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new BuilderException('Mandatory data missing for: '.implode(', ', $diff));
        }

        if (empty($data['access_token'])) {
            throw new BuilderException('access_token is mandatory and should not be empty');
        }

        return new AccessToken(
            $data['access_token'],
            $data['token_type'],
            $data['app_id'],
            $data['expires_in'],
            $data['scope']
        );
    }
}
