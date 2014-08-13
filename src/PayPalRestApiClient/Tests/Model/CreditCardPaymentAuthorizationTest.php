<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Model\CreditCardPaymentAuthorization;
use PayPalRestApiClient\Model\Payer;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\Link;
use PayPalRestApiClient\Model\Authorization;
use PayPalRestApiClient\Model\Amount;

class CreditCardPaymentAuthorizationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAuthorization()
    {
        $authorization = new Authorization(
            '55660361T84491906',
            '2014-08-06T17:09:29Z',
            '2014-08-06T17:09:29Z',
            new Amount('EUR', '12.00'),
            'authorized',
            'PAY-82D19789V6611622NKPSQISI',
            '2014-09-06T17:09:29Z',
            array(
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906',
                    'self',
                    'GET'
                ),
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/capture',
                    'capture',
                    'POST'
                ),
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/void',
                    'void',
                    'POST'
                ),
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                    'parent_payment',
                    'GET'
                )
            )
        );
        $obj = new CreditCardPaymentAuthorization(
            'PAY-ID',
            "2014-08-08T17:11:00Z",
            "2014-08-08T17:11:00Z",
            "approved",
            "authorize",
            new Payer(
                'credit_card',
                array(
                    array(
                        'credit_card' => array(
                            'number' => '4417119669820331',
                            'type' => 'visa',
                            'expire_month' => 11,
                            'expire_year' => 2018,
                            'cvv2' => '874',
                            'first_name' => 'Betsy',
                            'last_name' => 'Buyer',
                            'billing_address' => array(
                                'line1' => '111 First Street',
                                'city' => 'Saratoga',
                                'state' => 'CA',
                                'postal_code' => '95070',
                                'country_code' => 'US'
                            )
                        )
                    )
                )
            ),
            array(
                new Transaction(
                    new Amount('EUR', '12.00'),
                    'my transaction',
                    array(),
                    array(
                        array(
                            'authorization' => $authorization
                        )
                    )
                )
            ),
            array(
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                    'self',
                    'GET'
                )
            )
        );

        $this->assertInstanceOf('PayPalRestApiClient\Model\Authorization', $obj->getAuthorization());
        $this->assertEquals(
            $authorization,
            $obj->getAuthorization()
        );
    }

    public function testGetLinksUrl()
    {
        $authorization = new Authorization(
            '55660361T84491906',
            '2014-08-06T17:09:29Z',
            '2014-08-06T17:09:29Z',
            new Amount('EUR', '12.00'),
            'authorized',
            'PAY-82D19789V6611622NKPSQISI',
            '2014-09-06T17:09:29Z',
            array(
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906',
                    'self',
                    'GET'
                ),
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/capture',
                    'capture',
                    'POST'
                ),
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/void',
                    'void',
                    'POST'
                ),
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                    'parent_payment',
                    'GET'
                )
            )
        );
        $obj = new CreditCardPaymentAuthorization(
            'PAY-ID',
            "2014-08-08T17:11:00Z",
            "2014-08-08T17:11:00Z",
            "approved",
            "authorize",
            new Payer(
                'credit_card',
                array(
                    array(
                        'credit_card' => array(
                            'number' => '4417119669820331',
                            'type' => 'visa',
                            'expire_month' => 11,
                            'expire_year' => 2018,
                            'cvv2' => '874',
                            'first_name' => 'Betsy',
                            'last_name' => 'Buyer',
                            'billing_address' => array(
                                'line1' => '111 First Street',
                                'city' => 'Saratoga',
                                'state' => 'CA',
                                'postal_code' => '95070',
                                'country_code' => 'US'
                            )
                        )
                    )
                )
            ),
            array(
                new Transaction(
                    new Amount('EUR', '12.00'),
                    'my transaction',
                    array(),
                    array(
                        array(
                            'authorization' => $authorization
                        )
                    )
                )
            ),
            array(
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                    'self',
                    'GET'
                )
            )
        );

        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/capture',
            $obj->getCaptureUrl()
        );
    }
}
