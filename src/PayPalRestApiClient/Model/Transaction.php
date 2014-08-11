<?php

namespace PayPalRestApiClient\Model;

/**
 * The Transaction class represents a paypal transaction object
 */
class Transaction implements TransactionInterface
{
    protected $amount;
    protected $description;
    protected $itemList;
    protected $relatedResources;

    /**
     * Construct 
     *
     * @param PayPalRestApiClient\Model\AmountInterface $amount not null
     * @param string $description default null
     * @param array $itemList default empty array
     * @param array $relatedResources default empty array
     *
     * @see https://developer.paypal.com/docs/api/#transaction-object
     */
    public function __construct(AmountInterface $amount, $description = null, $itemList = array(), $relatedResources = array())
    {
        $this->amount = $amount;
        $this->description = $description;
        $this->itemList = $itemList;
        $this->relatedResources = $relatedResources;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getItemList()
    {
        return $this->itemList;
    }

    public function getRelatedResources()
    {
        return $this->relatedResources;
    }
}