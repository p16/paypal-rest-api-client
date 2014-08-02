<?php

namespace PayPalRestApiClient\Model;

use PayPalRestApiClient\Exception\AccessTokenException;

class AccessToken
{
    protected $scope;
    protected $accessToken;
    protected $tokenType;
    protected $appId;
    protected $expiresIn;

    /**
     * @see https://developer.paypal.com/docs/api/#authentication--headers
     */
    private function __construct($accessToken, $tokenType, $appId, $expiresIn, $scope)
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->appId = $appId;
        $this->expiresIn = $expiresIn;
        $this->scope = $scope;
    }

    public static function fromArray(array $data)
    {
        $mandatoryKeys = array(
            'scope', 'access_token', 'token_type', 'app_id', 'expires_in',
        );
        $diff = array_diff($mandatoryKeys, array_keys($data));
        if (count($diff) > 0) {
            throw new AccessTokenException('Mandatory data missing for: '.implode(', ', $diff));
        }

        if (empty($data['access_token'])) {
            throw new AccessTokenException('access_token is mandatory and should not be empty');
        }

        return new self(
            $data['access_token'],
            $data['token_type'],
            $data['app_id'],
            $data['expires_in'],
            $data['scope']
        );
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getTokenType()
    {
        return $this->tokenType;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    public function getScope()
    {
        return $this->scope;
    }
}