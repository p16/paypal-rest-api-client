<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\CaptureBuilder;

class CaptureBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new CaptureBuilder();
    }

    public function testBuild()
    {
        $data = array(
            'id' => 'PAY-ID',
            'create_time' => "2014-08-08T17:11:00Z",
            'update_time' => "2014-08-08T17:11:00Z",
            'amount' => array(
                'total' => 12.00,
                'currency' => 'EUR'
            ),
            'is_final_capture' => true,
            'state' => 'copmpleted',
            'parent_payment' => 'PAY-ID-ID',
            'links' => array(
                array(
                    'href' => 'https://link',
                    'rel' => 'self',
                    'method' => 'GET',
                )
            )
        );
        $capture = $this->builder->build($data);

        $this->assertInstanceOf('PayPalRestApiClient\Model\Capture', $capture);
    }

    /**
     * @expectedException PayPalRestApiClient\Exception\BuilderException
     * @expectedExceptionMessage Mandatory data missing for: id, create_time, update_time, amount, is_final_capture, state, parent_payment, links
     */
    public function testBuildValidationNoScope()
    {
        $data = array();
        $authorization = $this->builder->build($data);
    }
}
