<?php

namespace PayPalRestApiClient\Model;

/**
 * The PaymentAuthorization class represents a paypal payment authorization object
 */
abstract class AbstractPayment
{
    use \PayPalRestApiClient\Traits\PaypalData;

    protected $id;
    protected $createTime;
    protected $updateTime;
    protected $state;
    protected $intent;
    protected $payer;
    protected $transactions;
    protected $links;

    /**
     * Construct 
     *
     * @param string $id not null
     * @param string $createTime not null
     * @param string $updateTime not null
     * @param string $state not null
     * @param string $intent not null
     * @param string $payer not null
     * @param array $transactions not null
     * @param array $links not null
     */
    public function __construct(
        $id,
        $createTime,
        $updateTime,
        $state,
        $intent,
        $payer,
        array $transactions,
        array $links
    ) {
        $this->id = $id;
        $this->createTime = $createTime;
        $this->updateTime = $updateTime;
        $this->state = $state;
        $this->intent = $intent;
        $this->payer = $payer;
        $this->transactions = $transactions;
        $this->links = $links;

        $this->initUrls();
    }

    abstract protected function initUrls();

    public function getId()
    {
        return $this->id;
    }

    public function getCreateTime()
    {
        return $this->createTime;
    }

    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getIntent()
    {
        return $this->intent;
    }

    public function getPayer()
    {
        return $this->payer;
    }

    public function getTransactions()
    {
        return $this->transactions;
    }

    public function getLinks()
    {
        return $this->links;
    }
}