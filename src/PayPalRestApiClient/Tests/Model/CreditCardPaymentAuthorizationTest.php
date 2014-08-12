<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Model\CreditCardPaymentAuthorization;

class CreditCardPaymentAuthorizationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAuthorization()
    {
        $authorization = array(
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
        );
        $obj = new CreditCardPaymentAuthorization(
            'PAY-ID', "2014-08-08T17:11:00Z", "2014-08-08T17:11:00Z",
            "approved", "authorize",
            array(
                'payment_method' => 'credit_card',
                'funding_instruments' => array(
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
                ),
            ),
            array(
                array(
                    array(
                        'amount' => array(
                            'total' => 12.00,
                            'currency' => 'EUR'
                        )
                    ),
                    'description' => 'my transaction',
                    'related_resources' => array(
                        array(
                            'authorization' => $authorization
                        )
                    )
                )
            ),
            array(
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                    'rel' => 'self',
                    'method' => 'GET',
                )
            )
        );

        $this->assertTrue(is_array($obj->getAuthorization()));
        $this->assertEquals(
            $authorization,
            $obj->getAuthorization()
        );
    }

    public function testGetLinksUrl()
    {
        $obj = new CreditCardPaymentAuthorization(
            'PAY-ID', "2014-08-08T17:11:00Z", "2014-08-08T17:11:00Z",
            "approved", "authorize",
            array(
                'payment_method' => 'credit_card',
                'funding_instruments' => array(
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
                ),
            ),
            array(
                array(
                    array(
                        'amount' => array(
                            'total' => 12.00,
                            'currency' => 'EUR'
                        )
                    ),
                    'description' => 'my transaction',
                    'related_resources' => array(
                        array(
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
                )
            ),
            array(
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                    'rel' => 'self',
                    'method' => 'GET',
                )
            )
        );

        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/capture',
            $obj->getCaptureUrl()
        );
    }
}
