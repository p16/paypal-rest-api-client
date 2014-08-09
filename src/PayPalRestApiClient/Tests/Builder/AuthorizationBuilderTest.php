<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\AuthorizationBuilder;

class AuthorizationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new AuthorizationBuilder();
    }

    public function testBuild()
    {
        $data = array(
            'id' => 'PAY-ID',
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
            'links' => array(
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-82D19789V6611622NKPSQISI',
                    'rel' => 'self',
                    'method' => 'GET',
                )
            )
        );
        $authorization = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Authorization', $authorization);
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
