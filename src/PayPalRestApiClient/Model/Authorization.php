<?php

namespace PayPalRestApiClient\Model;

/**
 * The Authorization class represents a paypal authorization object
 */
class Authorization
{
    use \PayPalRestApiClient\Traits\AssertArrayOfObject;
    use \PayPalRestApiClient\Traits\LinkFinder;

    protected $id;
    protected $createTime;
    protected $updateTime;
    protected $amount;
    protected $state;
    protected $parentPayment;
    protected $validUntil;

    public function __construct(
        $id,
        $createTime,
        $updateTime,
        AmountInterface $amount,
        $state,
        $parentPayment,
        $validUntil,
        array $links
    ) {
        $this->id = $id;
        $this->createTime = $createTime;
        $this->updateTime = $updateTime;
        $this->amount = $amount;
        $this->state = $state;
        $this->parentPayment = $parentPayment;
        $this->validUntil = $validUntil;

        $this->assertArrayOfObjects($links, 'PayPalRestApiClient\Model\LinkInterface', 'links');
        $this->links = $links;
    }

    /**
     * @return string
     */
    public function getCaptureUrl()
    {
        $link = $this->findLink('capture');

        return $link->getHref();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * @return PayPalRestApiClient\Model\AmountInterface instance
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getParentPayment()
    {
        return $this->parentPayment;
    }

    /**
     * @return string
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * @return array of PayPalRestApiClient\Model\LinkInterface instances
     */
    public function getLinks()
    {
        return $this->links;
    }
}