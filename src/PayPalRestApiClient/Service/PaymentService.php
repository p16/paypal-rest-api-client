<?php

namespace PayPalRestApiClient\Service;

use Guzzle\Http\Client;
use PayPalRestApiClient\Model\AccessToken;
use PayPalRestApiClient\Model\Payment;
use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;
use Guzzle\Http\Exception\ClientErrorResponseException;
use PayPalRestApiClient\Builder\PaymentBuilder;

class PaymentService
{
    protected $client;
    protected $baseUrl;
    protected $paymentRequestBodyBuilder;
    protected $debug;

    public function __construct(
        Client $client,
        PaymentRequestBodyBuilder $paymentRequestBodyBuilder,
        $baseUrl,
        $debug = false
    ) {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->paymentRequestBodyBuilder = $paymentRequestBodyBuilder;
        $this->debug = $debug;
    }

    public function execute(
        AccessToken $accessToken,
        Payment $payment,
        $payerId
    ) {
        $request = $this->client->createRequest(
            'POST',
            $payment->getExecuteUrl(),
            array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Authorization' => $accessToken->getTokenType().' '.$accessToken->getAccessToken(),
                'Content-Type' => 'application/json'
            ),
            '{"payer_id":"'.$payerId.'"}',
            array(
                'debug' => $this->debug
            )
        );

        try {
            $response = $this->client->send($request);
        }
        catch (ClientErrorResponseException $e) {

            $response = $e->getResponse();
            $details = json_decode($response->getBody(), true);

            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                "reason '".$details['error']."' ".$details['error_description']
            );
        }

        if (200 != $response->getStatusCode()) {
            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        $data = json_decode($response->getBody(), true);

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;
    }

    public function capture(AccessToken $accessToken, Payment $payment, $isFinalCapture = true)
    {
        $amount = $payment->getAmount();
        $data = array(
            'amount' => array(
                'total' => $amount->getTotal(),
                'currency' => $amount->getCurrency()
            ),
            'is_final_capture' => $isFinalCapture
        );

        $request = $this->client->createRequest(
            'POST',
            $payment->getCaptureUrls()[0],
            array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Authorization' => $accessToken->getTokenType().' '.$accessToken->getAccessToken(),
                'Content-Type' => 'application/json'
            ),
            json_encode($data),
            array(
                'debug' => $this->debug
            )
        );

        try {
            $response = $this->client->send($request);
        }
        catch (ClientErrorResponseException $e) {

            $response = $e->getResponse();
            $details = json_decode($response->getBody(), true);

            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                "reason '".$details['error']."' ".$details['error_description']
            );
        }

        if (200 != $response->getStatusCode()) {
            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        $data = json_decode($response->getBody(), true);

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;
    }

    public function authorize(
        AccessToken $accessToken,
        $payer,
        $urls,
        $transactions
    ) {
        $requestBody = $this->paymentRequestBodyBuilder->build(
            'authorize',
            $payer,
            $urls,
            $transactions
        );

        $request = $this->buildRequest($accessToken, $requestBody);

        return $this->doSend($request);
    }

    public function create(
        AccessToken $accessToken,
        $payer,
        $urls,
        $transactions
    ) {
        $requestBody = $this->paymentRequestBodyBuilder->build(
            'sale',
            $payer,
            $urls,
            $transactions
        );

        $request = $this->buildRequest($accessToken, $requestBody);

        return $this->doSend($request);
    }

    protected function buildRequest(AccessToken $accessToken, array $requestBody)
    {
        $request = $this->client->createRequest(
            'POST',
            $this->baseUrl.'/v1/payments/payment',
            array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Authorization' => $accessToken->getTokenType().' '.$accessToken->getAccessToken(),
                'Content-Type' => 'application/json'
            ),
            json_encode($requestBody),
            array(
                'debug' => $this->debug
            )
        );

        return $request;
    }

    protected function doSend($request)
    {
        try {
            $response = $this->client->send($request);
        }
        catch (ClientErrorResponseException $e) {

            $response = $e->getResponse();
            $details = json_decode($response->getBody(), true);

            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase().", ".
                "reason '".$details['error']."' ".$details['error_description']
            );
        }

        if (201 != $response->getStatusCode()) {
            throw new PaymentException(
                "Payment error: ".
                "response status ".$response->getStatusCode()." ".$response->getReasonPhrase()
            );
        }

        $data = json_decode($response->getBody(), true);

        $paymentBuilder = new PaymentBuilder();
        $payment = $paymentBuilder->build($data);

        return $payment;        
    }
}