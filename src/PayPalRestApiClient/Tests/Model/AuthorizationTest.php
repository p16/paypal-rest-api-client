<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Model\Authorization;
use PayPalRestApiClient\Model\Link;
use PayPalRestApiClient\Model\Amount;

class AuthorizationTest extends \PHPUnit_Framework_TestCase
{
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

        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/authorization/55660361T84491906/capture',
            $authorization->getCaptureUrl()
        );
    }

    /**
     * @expectedException \RuntimeException
     @ @expectedExceptionMessage Cannot find a link corresponding to rel 'capture'
     */
    public function testGetLinksUrlException()
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

        $authorization->getCaptureUrl();
    }
}
