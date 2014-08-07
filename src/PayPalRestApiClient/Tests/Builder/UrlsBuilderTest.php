<?php

namespace PayPalRestApiClient\Tests;

use PayPalRestApiClient\Builder\UrlsBuilder;

class UrlsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->builder = new UrlsBuilder();
    }

    public function testBuildArray()
    {
        $urls = $expected = array('return_url' => 'url', 'cancel_url' => 'url');

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($urls)
        );
    }

    public function testBuildArrayExtraParameters()
    {
        $urls = array('return_url' => 'url', 'cancel_url' => 'url', 'example' => 'example');
        $expected = array('return_url' => 'url', 'cancel_url' => 'url');

        $this->assertEquals(
            $expected,
            $this->builder->buildArray($urls)
        );
    }

    public function constructParametersProvider()
    {
        return array(
            array(null),
            array(array()),
            array(array('return_url' => '')),
            array(array('cancel_url' => '')),
            array(array('extra_key' => '')),
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
