<?php

namespace PayPalRestApiClient\Builder;

use PayPalRestApiClient\Exception\BuilderException;
use PayPalRestApiClient\Model\Authorization;

/**
 * The AuthorizationBuilder builds instances of PayPalRestApiClient\Model\Authorization
 *
 * AuthorizationBuilder depends on two other builders: AmountBuilder and LinkBuilder.
 */
class AuthorizationBuilder extends AbstractBuilder
{
    protected $amountBuilder;
    protected $linkBuilder;

    public function __construct()
    {
        $this->amountBuilder = new AmountBuilder();
        $this->linkBuilder = new LinkBuilder();
    }

    public function setAmountBuilder($amountBuilder)
    {
        $this->amountBuilder = $amountBuilder;
    }

    public function setLinksBuilder($linkBuilder)
    {
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * Build an instance of PayPalRestApiClient\Model\Authorization given an array
     *
     * @param array $data The array should contains the following keys: 
     * amount, create_time, update_time, state, parent_payment, id, valid_until, links.
     * 
     * @return PayPalRestApiClient\Model\Authorization
     *
     * @throws PayPalRestApiClient\Exception\BuilderException
     * 
     * @see https://developer.paypal.com/docs/api/#authorization-object
     */
    public function build(array $data)
    {
        $this->validateArrayKeys(
            array('amount', 'create_time', 'update_time', 'state', 'parent_payment', 'id', 'valid_until', 'links'),
            $data
        );

        $links = array();
        foreach ($data['links'] as $link) {
            $links[] = $this->linkBuilder->build($link);
        }

        $authorization = new Authorization(
            $data['id'],
            $data['create_time'],
            $data['update_time'],
            $this->amountBuilder->build($data['amount']),
            $data['state'],
            $data['parent_payment'],
            $data['valid_until'],
            $links
        );

        return $authorization;
    }
}
