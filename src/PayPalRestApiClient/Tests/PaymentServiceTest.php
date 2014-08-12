<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Service\PaymentService;

class PaymentServiceTest extends PaymentServiceTestCase
{
    public function testCreateMultipleItems()
    {
        $status = 201;
        $json = '{"id":"PAY-15M32000F9063160GKPOXIAY","create_time":"2014-08-02T23:28:03Z","update_time":"2014-08-02T23:28:03Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","item_list":{"items":[{"name":"example","sku":"1","price":"5.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"2","price":"3.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"3","price":"7.00","currency":"EUR","quantity":"1"}],"shipping_address":{"recipient_name":"Fi Fi","line1":"Via del mare","city":"Milano","phone":"3213213211","postal_code":"60010","country_code":"IT"}}}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-2BD64194W6159691D","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY/execute","rel":"execute","method":"POST"}]}';
        $this->initResponse($status, $json);
        $this->initAccessToken('Bearer', '123abc123abc');

        $requestBody = array(
            'intent' => 'sale',
            'payer' => array(
                'payment_method' => 'paypal'
            ),
            'redirect_urls' => array(
                'return_url' => $this->returnUrl,
                'cancel_url' => $this->cancelUrl
            ),
            'transactions' => array(
                array(
                    'amount' => array(
                        'total' => $this->total,
                        'currency' => $this->currency,
                    ),
                    'description' => $this->description,
                    'item_list' => array(
                        'items' => array(
                            array(
                                'quantity' => '1',
                                'name' => 'example',
                                'price' => '5.00',
                                'currency' => 'EUR',
                                'sku' => '1',
                            ),
                            array(
                                'quantity' => '1',
                                'name' => 'example',
                                'price' => '3.00',
                                'currency' => 'EUR',
                                'sku' => '2',
                            ),
                            array(
                                'quantity' => '1',
                                'name' => 'example',
                                'price' => '7.00',
                                'currency' => 'EUR',
                                'sku' => '3',
                            )
                        ),
                        'shipping_address' => array(
                            'recipient_name' => 'Fi Fi',
                            'type' => 'residential',
                            'line1' => 'Via del mare',
                            'line2' => '',
                            'city' => 'Milano',
                            'country_code' => 'IT',
                            'postal_code' => '60010',
                            'state' => '',
                            'phone' => '3213213211',
                        )
                    )
                )
            ),
        );
        $this->initBuilder($requestBody, 'sale');
        $this->initClient($requestBody);

        $payer = $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $urls = array(
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl
        );
        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');

        $payment = $this->service->create(
            $this->accessToken,
            $payer,
            $urls,
            array($transaction)
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
    }
}
