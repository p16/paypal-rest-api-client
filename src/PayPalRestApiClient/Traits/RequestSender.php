<?php

namespace PayPalRestApiClient\Traits;

use Guzzle\Http\Exception\ClientErrorResponseException;
use PayPalRestApiClient\Exception\CallException;

trait RequestSender
{
    abstract protected function getClient();

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