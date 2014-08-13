<?php

namespace PayPalRestApiClient\Model;

/**
 * The PaymentAuthorization class represents a paypal payment authorization object
 */
abstract class AbstractPayment
{
    use \PayPalRestApiClient\Traits\PaypalData;
    use \PayPalRestApiClient\Traits\AssertArrayOfObject;

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
        PayerInterface $payer,
        array $transactions,
        array $links
    ) {
        $this->id = $id;
        $this->createTime = $createTime;
        $this->updateTime = $updateTime;
        $this->state = $state;
        $this->intent = $intent;
        $this->payer = $payer;

        $this->assertArrayOfObjects($transactions, 'PayPalRestApiClient\Model\TransactionInterface', 'transactions');
        $this->transactions = $transactions;

        $this->assertArrayOfObjects($links, 'PayPalRestApiClient\Model\LinkInterface', 'links');
        $this->links = $links;
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
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * @return string
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @return array
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
}