<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Model\PaypalPaymentAuthorization;
use PayPalRestApiClient\Model\Authorization;
use PayPalRestApiClient\Model\Amount;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\Link;
use PayPalRestApiClient\Model\Payer;

class PaypalPaymentAuthorizationTest extends \PHPUnit_Framework_TestCase
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
        $obj = new PaypalPaymentAuthorization(
            'PAY-ID', "2014-08-08T17:11:00Z",
            "approved", "authorize",
            new Payer('paypal'),
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
            ),
            "2014-08-08T17:11:00Z"
        );

        $this->assertInstanceOf(
            'PayPalRestApiClient\Model\Authorization',
            $obj->getAuthorization()
        );
        $this->assertEquals(
            $authorization,
            $obj->getAuthorization()
        );

        return $obj;
    }

    /**
     * @depends testGetAuthorization
     */
    public function testGetLinksUrl($obj)
    {
        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/capture',
            $obj->getCaptureUrl()
        );
    }
}
