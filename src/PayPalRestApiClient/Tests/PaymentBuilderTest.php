<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\PaymentBuilder;

class PaymentBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
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

        $this->assertInstanceOf('PayPalRestApiClient\Model\Payer', $payment->getPayer());
        $this->assertEquals('paypal', $payment->getPayer()->getPaymentMethod());
        $this->assertEquals(array('shipping_address' => array()), $payment->getPayer()->getInfo());
        $this->assertEquals(null, $payment->getPayer()->getFundingInstruments());

        $this->assertcount(1, $payment->getTransactions());
        $transaction = $payment->getTransactions()[0];

        $this->assertInstanceOf('PayPalRestApiClient\Model\Transaction', $transaction);
        $this->assertEquals('My fantastic transaction description', $transaction->getDescription());
        $this->assertEquals(array(), $transaction->getItemList());
        $this->assertEquals(array(), $transaction->getRelatedResources());

        $amount = $transaction->getAmount();
        $this->assertInstanceOf('PayPalRestApiClient\Model\Amount', $amount);
        $this->assertEquals('EUR', $amount->getCurrency());
        $this->assertEquals(14.77, $amount->getTotal());
        $this->assertEquals(array('subtotal' => 14.77), $amount->getDetails());

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
}
