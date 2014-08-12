<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\PaymentAuthorizationBuilder;

class PaymentAuthorizationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new PaymentAuthorizationBuilder();
    }

    public function testBuildPaypal()
    {
        $paypal_authorization_json = '{"id":"PAY-0R143116WW544010AKPU4P3I","create_time":"2014-08-12T07:53:17Z","update_time":"2014-08-12T07:53:17Z","state":"created","intent":"authorize","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"12.35","currency":"EUR","details":{"subtotal":"12.35"}},"description":"my transaction","item_list":{"items":[{"name":"product name","sku":"1233456789","price":"12.35","currency":"EUR","quantity":"1"}]}}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-42924620MK651460D","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-0R143116WW544010AKPU4P3I/execute","rel":"execute","method":"POST"}]}';
        $data = json_decode($paypal_authorization_json, true);

        $authorization = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\PaypalPaymentAuthorization', $authorization);
    }

    public function testBuildCreditCard()
    {
        $credit_card_authorization_json = '{"id":"PAY-2LS84841MB1756502KPSQJJA","create_time":"2014-08-08T17:11:00Z","update_time":"2014-08-08T17:11:19Z","state":"approved","intent":"authorize","payer":{"payment_method":"credit_card","funding_instruments":[{"credit_card":{"type":"visa","number":"xxxxxxxxxxxx0331","expire_month":"11","expire_year":"2018","first_name":"Betsy","last_name":"Buyer","billing_address":{"line1":"111 First Street","city":"Saratoga","state":"CA","postal_code":"95070","country_code":"US"}}}]},"transactions":[{"amount":{"total":"12.35","currency":"EUR","details":{"subtotal":"12.35"}},"description":"my transaction","related_resources":[{"authorization":{"id":"6JK78052MJ7446007","create_time":"2014-08-08T17:11:00Z","update_time":"2014-08-08T17:11:19Z","amount":{"total":"12.35","currency":"EUR","details":{"subtotal":"12.35"}},"state":"authorized","parent_payment":"PAY-2LS84841MB1756502KPSQJJA","valid_until":"2014-09-06T17:11:00Z","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007/capture","rel":"capture","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/authorization/6JK78052MJ7446007/void","rel":"void","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-2LS84841MB1756502KPSQJJA","rel":"parent_payment","method":"GET"}]}}]}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-2LS84841MB1756502KPSQJJA","rel":"self","method":"GET"}]}';
        $data = json_decode($credit_card_authorization_json, true);

        $authorization = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\CreditCardPaymentAuthorization', $authorization);
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Mandatory data missing for: id, create_time, update_time, state, intent, payer, transactions, links
     */
    public function testBuildValidationNoScope()
    {
        $data = array();
        $authorization = $this->builder->build($data);
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage id is mandatory and should not be empty
     */
    public function testBuildValidationEmptyAccessToken()
    {
        $data = array(
            'id' => '',
            'create_time' => "2014-08-08T17:11:00Z",
            'update_time' => "2014-08-08T17:11:00Z",
            'state' => "approved",
            'intent' => "authorize",
            'payer' => array(
                'payment_method' => 'paypal',
            ),
            'transactions' => array(
                array(
                    array(
                        'amount' => array(
                            'total' => 12.00,
                            'currency' => 'EUR'
                        )
                    ),
                    'description' => 'my transaction',
                    'related_resources' => array(
                        'authorization' => array(
                            'id' => '55660361T84491906',
                            'create_time' => '',
                            'update_time' => '',
                            'amount' => array(
                                'total' => 12.00,
                                'currency' => 'EUR'
                            ),
                            'state' => 'authorized',
                            'parent_payment' => 'PAY-82D19789V6611622NKPSQISI',
                            'valid_until' => '2014-09-06T17:09:29Z',
                            'links' => array(
                                array(
                                    'href' => 'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906',
                                    'rel' => 'self',
                                    'method' => 'GET',
                                ),
                                array(
                                    'href' => 'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/capture',
                                    'rel' => 'capture',
                                    'method' => 'POST',
                                ),
                                array(
                                    'href' => 'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/void',
                                    'rel' => 'void',
                                    'method' => 'POST',
                                ),
                                array(
                                    'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                                    'rel' => 'parent_payment',
                                    'method' => 'GET',
                                )
                            )
                        )
                    )
                )
            ),
            'links' => array(
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                    'rel' => 'self',
                    'method' => 'GET',
                )
            )
        );
        $authorization = $this->builder->build($data);
    }
}
