<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;

class PaymentRequestBodyBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $payerBuilder = $this->getMock('PayPalRestApiClient\Builder\PayerBuilder');
        $urlsBuilder = $this->getMock('PayPalRestApiClient\Builder\UrlsBuilder');
        $transactionsBuilder = $this->getMock('PayPalRestApiClient\Builder\TransactionsBuilder');

        $builder = new PaymentRequestBodyBuilder(
            $payerBuilder,
            $urlsBuilder,
            $transactionsBuilder
        );

        $this->assertInstanceOf('PayPalRestApiClient\Builder\PaymentRequestBodyBuilder', $builder);
    }

    public function testBuild()
    {
        $payerData = array('payment_method' => 'paypal_method');
        $payer = $this->getMock('PayPalRestApiClient\Model\PayerInterface');
        $payerBuilder = $this->getMock('PayPalRestApiClient\Builder\PayerBuilder');
        $payerBuilder->expects($this->once())
            ->method('buildArray')
            ->with($payer)
            ->will($this->returnValue($payerData));

        $urls = array(
            'return_url' => 'url',
            'cancel_url' => 'url'
        );
        $urlsBuilder = $this->getMock('PayPalRestApiClient\Builder\UrlsBuilder');
        $urlsBuilder->expects($this->once())
            ->method('buildArray')
            ->with($urls)
            ->will($this->returnValue($urls));

        $transactionsData = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            )
        );
        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');
        $transactionsBuilder = $this->getMock('PayPalRestApiClient\Builder\TransactionsBuilder');
        $transactionsBuilder->expects($this->once())
            ->method('buildArray')
            ->with(array($transaction))
            ->will($this->returnValue($transactionsData));

        $expectedData = array(
            'intent' => 'authorize',
            'payer' => $payerData,
            'redirect_urls' => $urls,
            'transactions' => $transactionsData,
        );

        $builder = new PaymentRequestBodyBuilder(
            $payerBuilder,
            $urlsBuilder,
            $transactionsBuilder
        );

        $data = $builder->build(
            'authorize',
            $payer,
            array('return_url' => 'url', 'cancel_url' => 'url'),
            array($transaction)
        );

        $this->assertEquals($expectedData, $data);
    }
}
