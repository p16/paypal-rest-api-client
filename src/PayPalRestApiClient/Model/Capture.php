<?php

namespace PayPalRestApiClient\Model;

/**
 * The Capture class represents a paypal capture object
 */
class Capture
{
    use \PayPalRestApiClient\Traits\PaypalData;

    protected $id;
    protected $createTime;
    protected $updateTime;
    protected $amount;
    protected $isFinalCapture;
    protected $state;
    protected $parentPayment;
    protected $links;

    /**
     * Construct 
     *
     * @param string $id not null
     * @param string $createTime not null
     * @param string $updateTime not null
     * @param PayPalRestApiClient\Model\AmountInterface $amount not null
     * @param boolean $isFinalCapture not null
     * @param string $state not null
     * @param string $parentPayment not null
     * @param array $links not null
     *
     * @see https://developer.paypal.com/docs/api/#capture-object
     */
    public function __construct(
        $id,
        $createTime,
        $updateTime,
        AmountInterface $amount,
        $isFinalCapture,
        $state,
        $parentPayment,
        array $links
    ) {
        $this->id = $id;
        $this->createTime = $createTime;
        $this->updateTime = $updateTime;
        $this->amount = $amount;
        $this->isFinalCapture = $isFinalCapture;
        $this->state = $state;
        $this->parentPayment = $parentPayment;
        $this->links = $links;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function isFinalCapture()
    {
        return $this->isFinalCapture;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getParentPayment()
    {
        return $this->parentPayment;
    }

    public function getLinks()
    {
        return $this->links;
    }
}