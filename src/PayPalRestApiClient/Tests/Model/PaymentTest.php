<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Model\Payment;
use PayPalRestApiClient\Model\Payer;
use PayPalRestApiClient\Model\Link;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLinksUrl()
    {
        $payment = new Payment(
            "PAY-6T42818722685883WKPPAT6I",
            "2014-08-03T10:07:53Z",
            "2014-08-03T10:07:53Z",
            'created',
            'sale',
            new Payer('paypal'),
            array(),
            array(
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I',
                    'self',
                    'GET'
                ),
                new Link(
                    'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-9VK533621R3302713',
                    'approval_url',
                    'REDIRECT'
                ),
                new Link(
                    'https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I/execute',
                    'execute',
                    'POST'
                ),
            )
        );

        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I/execute',
            $payment->getExecuteUrl()
        );

        $this->assertEquals(
            'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-9VK533621R3302713',
            $payment->getApprovalUrl()
        );
    }

    public function testGetAmount()
    {
        $this->markTestIncomplete();
    }
}
