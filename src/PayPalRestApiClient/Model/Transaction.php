<?php

namespace PayPalRestApiClient\Model;

/**
 * The Transaction class represents a paypal transaction object
 */
class Transaction implements TransactionInterface
{
    use \PayPalRestApiClient\Traits\AssertArrayOfObject;

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

        if (isset($this->relatedResources[0]['authorization'])) {
            $authorization = $this->relatedResources[0]['authorization'];
            $this->assertArrayOfObjects(array($authorization), 'PayPalRestApiClient\Model\Authorization', 'authorization');
        }

        $this->relatedResources = $relatedResources;
    }

    /**
     * @return PayPalRestApiClient\Model\Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getItemList()
    {
        return $this->itemList;
    }

    /**
     * @return array
     */
    public function getRelatedResources()
    {
        return $this->relatedResources;
    }

    /**
     * @return PayPalRestApiClient\Model\Authorization|null
     */
    public function getAuthorization()
    {
        if (isset($this->relatedResources[0]['authorization'])) {
            return $this->relatedResources[0]['authorization'];
        }
    }
}