<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Validator\PayPalJsonSchemaValidator;

class SchemaJsonValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function buildValidator($schema, $data)
    {
        $validator = new PayPalJsonSchemaValidator($schema, $data);

        return $validator;
    }

    public function testPaymentJson()
    {
        $validator = $this->buildValidator(
            'payment.json',
            json_decode(file_get_contents(__DIR__.'/data_payment.json'))
        );

        $this->assertTrue($validator->isValid());
    }

    public function testPaymentCreditCardRequestJson()
    {
        $validator = $this->buildValidator(
            'payment_request.json',
            json_decode(file_get_contents(__DIR__.'/data_request_credit_card.json'))
        );

        $this->assertTrue($validator->isValid());
    }

    public function testPaymentExecuteJson()
    {
        $validator = $this->buildValidator(
            'payment.json',
            json_decode(file_get_contents(__DIR__.'/data_execute.json'))
        );

        $this->assertTrue($validator->isValid());
    }

    public function testPaymentCaptureJson()
    {
        $validator = $this->buildValidator(
            'capture.json',
            json_decode(file_get_contents(__DIR__.'/data_capture.json'))
        );

        $this->assertTrue($validator->isValid());
    }

    public function testPaymentAccessTokenJson()
    {
        $validator = $this->buildValidator(
            'access_token.json',
            json_decode(file_get_contents(__DIR__.'/data_access_token.json'))
        );

        $this->assertTrue($validator->isValid());
    }

    public function testPaymentPaypalRequestJson()
    {
        $validator = $this->buildValidator(
            'payment_request.json',
            json_decode(file_get_contents(__DIR__.'/data_request_paypal.json'))
        );

        $this->assertTrue($validator->isValid());
    }

    public function testRedirectUrls()
    {
        $validator = $this->buildValidator(
            'redirect_urls.json',
            json_decode('{"return_url":"http:\/\/example.com\/success","cancel_url":"http:\/\/example.com\/cancel"}')
        );

        $this->assertTrue($validator->isValid());
    }
}