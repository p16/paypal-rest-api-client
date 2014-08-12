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
            array('payment_method' => 'paypal'),
            array(),
            array(
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I',
                    'rel' => 'self',
                    'method' => 'GET'
                ),
                array(
                    'href' => 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-9VK533621R3302713',
                    'rel' => 'approval_url',
                    'method' => 'REDIRECT'
                ),
                array(
                    'href' => 'https://api.sandbox.paypal.com/v1/payments/payment/PAY-6T42818722685883WKPPAT6I/execute',
                    'rel' => 'execute',
                    'method' => 'POST'
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
