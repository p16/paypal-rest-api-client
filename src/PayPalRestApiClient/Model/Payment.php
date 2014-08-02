<?php

namespace PayPalRestApiClient\Model;

class Payment
{
    protected $id;
    protected $createTime;
    protected $updateTime;
    protected $state;
    protected $intent;
    protected $payer;
    protected $transactions;
    protected $links;

    public function __construct(
        $id,
        $createTime,
        $updateTime,
        $state,
        $intent,
        Payer $payer,
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
    }

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