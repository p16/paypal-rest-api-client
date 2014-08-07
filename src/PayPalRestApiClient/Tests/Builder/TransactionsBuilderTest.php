<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\TransactionsBuilder;

class TransactionsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new TransactionsBuilder();
    }

    public function testBuildArrayFromObjects()
    {
        $amount = $this->getMock('PayPalRestApiClient\Model\AmountInterface');
        $amount->expects($this->once())
            ->method('getTotal')
            ->will($this->returnValue('15.00'));
        $amount->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue('EUR'));

        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');
        $transaction->expects($this->atLeastOnce())
            ->method('getAmount')
            ->will($this->returnValue($amount));
        $transaction->expects($this->once())
            ->method('getDescription')
            ->will($this->returnValue('My description'));

        $transactions = array($transaction);

        $expected = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            )
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($transactions)
        );
    }

    public function testBuildArrayFromObjectsMultipleTransactions()
    {
        $amount = $this->getMock('PayPalRestApiClient\Model\AmountInterface');
        $amount->expects($this->atLeastOnce())
            ->method('getTotal')
            ->will($this->returnValue('15.00'));
        $amount->expects($this->atLeastOnce())
            ->method('getCurrency')
            ->will($this->returnValue('EUR'));

        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');
        $transaction->expects($this->atLeastOnce())
            ->method('getAmount')
            ->will($this->returnValue($amount));
        $transaction->expects($this->atLeastOnce())
            ->method('getDescription')
            ->will($this->returnValue('My description'));

        $transactions = array($transaction, $transaction);

        $expected = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            ),
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            )
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($transactions)
        );
    }

    public function testBuildArrayFromObjectsWithMultipleItems()
    {
        $amount = $this->getMock('PayPalRestApiClient\Model\AmountInterface');
        $amount->expects($this->once())
            ->method('getTotal')
            ->will($this->returnValue('15.00'));
        $amount->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue('EUR'));

        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');
        $transaction->expects($this->atLeastOnce())
            ->method('getAmount')
            ->will($this->returnValue($amount));
        $transaction->expects($this->once())
            ->method('getDescription')
            ->will($this->returnValue('My description'));
        $transaction->expects($this->once())
            ->method('getItemList')
            ->will($this->returnValue(
                array(
                    'items' => array(
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '5.00',
                            'currency' => 'EUR',
                            'sku' => '1',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '3.00',
                            'currency' => 'EUR',
                            'sku' => '2',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '7.00',
                            'currency' => 'EUR',
                            'sku' => '3',
                        )
                    )
                )
            ));

        $transactions = array($transaction);

        $expected = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description',
                'item_list' => array(
                    'items' => array(
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '5.00',
                            'currency' => 'EUR',
                            'sku' => '1',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '3.00',
                            'currency' => 'EUR',
                            'sku' => '2',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '7.00',
                            'currency' => 'EUR',
                            'sku' => '3',
                        )
                    )
                )
            )
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($transactions)
        );
    }

    public function testBuildArrayFromObjectsWithMultipleItemsAndShippingAddress()
    {
        $amount = $this->getMock('PayPalRestApiClient\Model\AmountInterface');
        $amount->expects($this->once())
            ->method('getTotal')
            ->will($this->returnValue('15.00'));
        $amount->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue('EUR'));

        $transaction = $this->getMock('PayPalRestApiClient\Model\TransactionInterface');
        $transaction->expects($this->atLeastOnce())
            ->method('getAmount')
            ->will($this->returnValue($amount));
        $transaction->expects($this->once())
            ->method('getDescription')
            ->will($this->returnValue('My description'));
        $transaction->expects($this->once())
            ->method('getItemList')
            ->will($this->returnValue(
                array(
                    'items' => array(
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '5.00',
                            'currency' => 'EUR',
                            'sku' => '1',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '3.00',
                            'currency' => 'EUR',
                            'sku' => '2',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '7.00',
                            'currency' => 'EUR',
                            'sku' => '3',
                        )
                    ),
                    'shipping_address' => array(
                        'recipient_name' => 'Fi Fi',
                        'type' => 'residential',
                        'line1' => 'Via del mare',
                        'line2' => '',
                        'city' => 'Milano',
                        'country_code' => 'IT',
                        'postal_code' => '60010',
                        'state' => '',
                        'phone' => '3213213211',
                    )
                )
            ));

        $transactions = array($transaction);

        $expected = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description',
                'item_list' => array(
                    'items' => array(
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '5.00',
                            'currency' => 'EUR',
                            'sku' => '1',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '3.00',
                            'currency' => 'EUR',
                            'sku' => '2',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '7.00',
                            'currency' => 'EUR',
                            'sku' => '3',
                        )
                    ),
                    'shipping_address' => array(
                        'recipient_name' => 'Fi Fi',
                        'type' => 'residential',
                        'line1' => 'Via del mare',
                        'line2' => '',
                        'city' => 'Milano',
                        'country_code' => 'IT',
                        'postal_code' => '60010',
                        'state' => '',
                        'phone' => '3213213211',
                    )
                )
            )
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($transactions)
        );
    }

    public function testBuildArrayFromArraysWithMultipleItems()
    {
        $transactions = $expected = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description',
                'item_list' => array(
                    'items' => array(
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '5.00',
                            'currency' => 'EUR',
                            'sku' => '1',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '3.00',
                            'currency' => 'EUR',
                            'sku' => '2',
                        ),
                        array(
                            'quantity' => 1,
                            'name' => 'example',
                            'price' => '7.00',
                            'currency' => 'EUR',
                            'sku' => '3',
                        )
                    ),
                    'shipping_address' => array(
                        'recipient_name' => 'Fi Fi',
                        'type' => 'residential',
                        'line1' => 'Via del mare',
                        'line2' => '',
                        'city' => 'Milano',
                        'country_code' => 'IT',
                        'postal_code' => '60010',
                        'state' => '',
                        'phone' => '3213213211',
                    )
                )
            )
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($transactions)
        );
    }

    public function testBuildArrayFromArrays()
    {
        $transactions = $expected = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            )
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($transactions)
        );
    }

    public function testBuildArrayFromArraysMultipleTransactions()
    {
        $transactions = $expected = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            ),
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            )
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($transactions)
        );
    }

    public function testBuildArrayExtraParameters()
    {
        $transactions = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                    'extra' => 'extra'
                ),
                'description' => 'My description',
                'extra' => 'extra'
            ),
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            )
        );

        $expected = array(
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            ),
            array(
                'amount' => array(
                    'total' => '15.00',
                    'currency' => 'EUR',
                ),
                'description' => 'My description'
            )
        );

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($transactions)
        );
    }

    public function constructParametersProvider()
    {
        return array(
            array(null),
            array(new \stdClass()),
            array(array('xyzsdfg')),
        );
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @dataProvider constructParametersProvider
     */
    public function testBuildArrayValidation($payer)
    {
        $this->builder->buildArray($payer);
    }
}
