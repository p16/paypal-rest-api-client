<?php

namespace PayPalRestApiClient\Model;

class Transaction
{
    protected $amount;
    protected $description;
    protected $itemList;
    protected $relatedResources;

    /**
     * @see https://developer.paypal.com/docs/api/#transaction-object
     */
    public function __construct(Amount $amount, $description = null, $itemList = array(), $relatedResources = array())
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