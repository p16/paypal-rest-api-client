<?php

namespace PayPalRestApiClient\Traits;

use Guzzle\Http\Exception\ClientErrorResponseException;
use PayPalRestApiClient\Exception\CallException;

/**
 * RequestSender trait define how handle request errors when making a call usgin a client
 */
trait RequestSender
{
    /**
     * @return mixed client obejct
     */
    abstract protected function getClient();

    /**
     * Sends a request and chatches exception if something does wrong.
     *
     * @param mixed $request
     * @param integer $acceptedStatusCode which http status code represens a "all is well" response for the call
     * @param string $errorLabel label that describes the context of an error
     *
     * @return mixed $response
     * @throws PayPalRestApiClient\Exception\CallException
     */
    public function send($request, $acceptedStatusCode = 200, $errorLabel = 'Error sending Request:')
    {
        try {
            $response = $this->getClient()->send($request);
        }
        catch (ClientErrorResponseException $e) {

            $response = $e->getResponse();
            $details = json_decode($response->getBody(), true);

            $reason = implode(
                ', ',
                array_map(
                    function ($vvalue, $key) {
                        return $key . ': ' . $vvalue;
                    }, 
                    $details,
                    array_keys($details)
                )
            );

            throw new CallException(
                $errorLabel.
                " response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                " reason: ".$reason
            );
        }

        if ($acceptedStatusCode != $response->getStatusCode()) {
            throw new CallException(
                $errorLabel.
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        return $response;
    }
}