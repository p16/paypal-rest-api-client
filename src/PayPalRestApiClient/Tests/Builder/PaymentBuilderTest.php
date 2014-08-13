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

        $payer = $payment->getPayer();
        $this->assertInstanceOf('PayPalRestApiClient\Model\Payer', $payer);
        $this->assertEquals('paypal', $payer->getPaymentMethod());
        $this->assertEquals(array('shipping_address' => array()), $payer->getInfo());

        $this->assertcount(1, $payment->getTransactions());
        $transaction = $payment->getTransactions()[0];

        $this->assertInstanceOf('PayPalRestApiClient\Model\Transaction', $transaction);
        $this->assertInstanceOf('PayPalRestApiClient\Model\Amount', $transaction->getAmount());
        $this->assertEquals('My fantastic transaction description', $transaction->getDescription());
        $this->assertEquals(array(), $transaction->getItemList());
        $this->assertEquals(array(), $transaction->getRelatedResources());
        $this->assertEquals(null, $transaction->getAuthorization());

        $links = $payment->getLinks();
        $this->assertcount(3, $links);

        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q',
            $links[0]->getHref()
        );
        $this->assertEquals('self', $links[0]->getRel());
        $this->assertEquals('GET', $links[0]->getMethod());

        $this->assertEquals(
            'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-26339740WK411984R',
            $links[1]->getHref()
        );
        $this->assertEquals('approval_url', $links[1]->getRel());
        $this->assertEquals('REDIRECT', $links[1]->getMethod());

        $this->assertEquals(
            'https://api.sandbox.paypal.com/v1/payments/payment/PAY-74S36081BM7699248KPOPD5Q/execute',
            $links[2]->getHref()
        );
        $this->assertEquals('execute', $links[2]->getRel());
        $this->assertEquals('POST', $links[2]->getMethod());
    }

    public function testBuildMultipleItems()
    {
        $paymentJson = '{"id":"PAY-15M32000F9063160GKPOXIAY","create_time":"2014-08-02T23:28:03Z","update_time":"2014-08-02T23:28:03Z","state":"created","intent":"sale","payer":{"payment_method":"paypal","payer_info":{"shipping_address":{}}},"transactions":[{"amount":{"total":"15.00","currency":"EUR","details":{"subtotal":"15.00"}},"description":"My fantastic transaction description","item_list":{"items":[{"name":"example","sku":"1","price":"5.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"2","price":"3.00","currency":"EUR","quantity":"1"},{"name":"example","sku":"3","price":"7.00","currency":"EUR","quantity":"1"}],"shipping_address":{"recipient_name":"Fi Fi","line1":"Via del mare","city":"Milano","phone":"3213213211","postal_code":"60010","country_code":"IT"}}}],"links":[{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY","rel":"self","method":"GET"},{"href":"https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-2BD64194W6159691D","rel":"approval_url","method":"REDIRECT"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-15M32000F9063160GKPOXIAY/execute","rel":"execute","method":"POST"}]}';
        $data = json_decode($paymentJson, true);

        $this->builder = new PaymentBuilder();
        $payment = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payment', $payment);

        $payer = $payment->getPayer();
        $this->assertInstanceOf('PayPalRestApiClient\Model\Payer', $payer);
        $this->assertEquals('paypal', $payer->getPaymentMethod());
        $this->assertEquals(array('shipping_address' => array()), $payer->getInfo());

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

        $this->assertInstanceOf('PayPalRestApiClient\Model\Transaction', $transaction);
        $this->assertInstanceOf('PayPalRestApiClient\Model\Amount', $transaction->getAmount());
        $this->assertEquals('My fantastic transaction description', $transaction->getDescription());
        $this->assertEquals($expectedItemList, $transaction->getItemList());
        $this->assertEquals(array(), $transaction->getRelatedResources());
        $this->assertEquals(null, $transaction->getAuthorization());

        $links = $payment->getLinks();
        $this->assertcount(3, $links);
    }
}
