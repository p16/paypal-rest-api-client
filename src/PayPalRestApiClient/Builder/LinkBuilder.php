<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Link;

/**
 * The LinkBuilder builds instances of PayPalRestApiClient\Model\Link
 */
class LinkBuilder extends AbstractBuilder
{
    /**
     * Build an instance of PayPalRestApiClient\Model\Link given an array
     *
     * @param array $data The array should contains the following keys: 
     * href, rel, method.
     * 
     * @return PayPalRestApiClient\Model\Link
     *
     * @throws PayPalRestApiClient\Exception\BuilderException
     * 
     * @see https://developer.paypal.com/docs/api/#hateoas-links
     */
    public function build(array $data)
    {
        $this->validateArrayKeys(
            array('href', 'rel', 'method'),
            $data
        );

        $link = new Link(
            $data['href'],
            $data['rel'],
            $data['method']
        );

        return $link;
    }
}
