<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\PaymentBuilder;

class PaymentBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildNoItems()
    {
        $paymentJson = '{"id":"PAY-74S36081BM7699248KPOPD5Q","create_time":"2014-08-02T14:13:10Z","update_time":"2014-08-02T14:13:10Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"14.77","currency":"EUR","details":{"subtotal":"14.77"}},"description":"My fantastic transaction description"}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-26339740WK411984R","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q/execute","rel":"execute","method":"POST"}]}';
        $data = json_decode($paymentJson, true);

        $this->builder = new PaymentBuilder();
        $payment = $this->builder->build($data);

        $this->assertEquals('PAY-74S36081BM7699248KPOPD5Q', $payment->getId());
        $this->assertEquals('2014-08-02T14:13:10Z', $payment->getCreateTime());
        $this->assertEquals('2014-08-02T14:13:10Z', $payment->getUpdateTime());
        $this->assertEquals('created', $payment->getState());
        $this->assertEquals('sale', $payment->getIntent());

        $this->assertTrue(is_array($payment->getPayer()));
        $this->assertEquals(
            array(
                'payment_method' => 'paypal',
                'payer_info' => array(
                    'shipping_address' => array()
                ),
            ),
            $payment->getPayer()
        );

        $this->assertcount(1, $payment->getTransactions());
        $transaction = $payment->getTransactions()[0];

        $this->assertTrue(is_array($transaction));
        $this->assertEquals(
            array(
                'amount' => array(
                    'total' => '14.77',
                    'currency' => 'EUR',
                    'details' => array(
                        'subtotal' => '14.77'
                    )
                ),
                'description' => 'My fantastic transaction description',
            ),
            $transaction
        );

        $links = $payment->getLinks();
        $this->assertcount(3, $links);

        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q',
            $links[0]['href']
        );
        $this->assertEquals('self', $links[0]['rel']);
        $this->assertEquals('GET', $links[0]['method']);

        $this->assertEquals(
            'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-26339740WK411984R',
            $links[1]['href']
        );
        $this->assertEquals('approval_url', $links[1]['rel']);
        $this->assertEquals('REDIRECT', $links[1]['method']);

        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q/execute',
            $links[2]['href']
        );
        $this->assertEquals('execute', $links[2]['rel']);
        $this->assertEquals('POST', $links[2]['method']);
    }

    public function testBuildMultipleItems()
    {
        $paymentJson = '{"id":"PAY-15M32000F9063160GKPOXIAY","create_time":"2014-08-02T23:28:03Z","update_time":"2014-08-02T23:28:03Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","item_list":{"items":[{"name":"example","sku":"1","price":"5.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"2","price":"3.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"3","price":"7.00","currency":"EUR","quantity":"1"}],"shipping_address":{"recipient_name":"Fi Fi","line1":"Via del mare","city":"Milano","phone":"3213213211","postal_code":"60010","country_code":"IT"}}}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-2BD64194W6159691D","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY/execute","rel":"execute","method":"POST"}]}';
        $data = json_decode($paymentJson, true);

        $this->builder = new PaymentBuilder();
        $payment = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);
        $this->assertEquals(
            array(
                'payment_method' => 'paypal',
                'payer_info' => array(
                    'shipping_address' => array()
                )
            ),
            $payment->getPayer()
        );

        $expectedItemList = array(
            'items' => array(
                array(
                    'name' => 'example',
                    'sku' => 1,
                    'price' => 5.00,
                    'currency' => 'EUR',
                    'quantity' => 1,
                ),
                array(
                    'name' => 'example',
                    'sku' => 2,
                    'price' => 3.00,
                    'currency' => 'EUR',
                    'quantity' => 1,
                ),
                array(
                    'name' => 'example',
                    'sku' => 3,
                    'price' => 7.00,
                    'currency' => 'EUR',
                    'quantity' => 1,
                )
            ),
            'shipping_address' => array(
                'recipient_name' => 'Fi Fi',
                'line1' => 'Via del mare',
                'city' => 'Milano',
                'phone' => '3213213211',
                'postal_code' => '60010',
                'country_code' => 'IT',
            )
        );
        $transaction = $payment->getTransactions()[0];
        $this->assertEquals(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                    'details' => array('subtotal' => '15.00')
                ),
                'description' => 'My fantastic transaction description',
                'item_list' => $expectedItemList
            ),
            $transaction
        );

        $links = $payment->getLinks();
        $this->assertcount(3, $links);
    }
}
