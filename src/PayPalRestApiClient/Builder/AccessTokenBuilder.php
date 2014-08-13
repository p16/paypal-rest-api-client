<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Model\AccessToken;
use PayPalRestApiClient\Exception\BuilderException;

/**
 * The AccessTokenBuilder builds instances of PayPalRestApiClient\Model\AccessToken
 */
class AccessTokenBuilder extends AbstractBuilder
{
    /**
     * Build an instance of PayPalRestApiClient\Model\AccessToken given an array
     *
     * @param array $data The array should contains the following keys: 
     * scope, access_token, token_type, app_id, expires_in.
     * The "access_token" key value should not be empty
     * 
     * @return PayPalRestApiClient\Model\AccessToken
     *
     * @throws PayPalRestApiClient\Exception\BuilderException If not all keys are set or when "access_token" is empty
     * 
     * @see https://developer.paypal.com/docs/api/#authentication--headers
     */
    public function build(array $data)
    {
        $this->validateArrayKeys(
            array('scope', 'access_token', 'token_type', 'app_id', 'expires_in'),
            $data
        );

        if (empty($data['access_token'])) {
            throw new BuilderException('access_token is mandatory and should not be empty');
        }

        $accessToken = new AccessToken(
            $data['access_token'],
            $data['token_type'],
            $data['app_id'],
            $data['expires_in'],
            $data['scope']
        );
        $accessToken->setPaypalData($data);

        return $accessToken;
    }
}
