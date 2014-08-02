<?php

namespace PayPalRestApi\Repository;

use Guzzle\Http\Client;
use PayPalRestApi\Model\AccessToken;
use PayPalRestApi\Exception\AccessTokenException;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 *
 */
class AccessTokenRepository
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Calling twice the getAccessToken method will give you two different token event for the same user.
     * For the time being, expires_in is not taken in consideration for this call.
     */
    public function getAccessToken($clientId, $secret)
    {
        $request = $this->client->createRequest(
            'POST',
            'https://api.sandbox.paypal.com/v1/oauth2/token',
            array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'grant_type=client_credentials',
            array(
                'auth' => array($clientId, $secret),
                'debug' => true
            )
        );

        try {
            $response = $this->client->send($request);
        }
        catch (ClientErrorResponseException $e) {

            $response = $e->getResponse();
            $details = json_decode($response->getBody(), true);

            throw new AccessTokenException(
                "Cannot retrieve access token: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                "reason '".$details['error']."' ".$details['error_description']
            );
        }

        if (200 != $response->getStatusCode())
        {
            throw new AccessTokenException(
                "Cannot retrieve access token: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        $data = json_decode($response->getBody(), true);

        return AccessToken::fromArray($data);
    }
}
