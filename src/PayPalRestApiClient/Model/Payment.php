<?php

namespace PayPalRestApiClient\Model;

class Payment
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
    protected $executeUrl;
    protected $approvalUrl;
    protected $captureUrls = array();

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

        $this->initUrls();
    }

    private function initUrls()
    {
        foreach ($this->links as $link) {
            switch ($link->getRel()) {
                case 'approval_url':
                    $this->approvalUrl = $link->getHref();
                    break;
                
                case 'execute':
                    $this->executeUrl = $link->getHref();
                    break;
            }
        }
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

    public function getExecuteUrl()
    {
        return $this->executeUrl;
    }

    public function getApprovalUrl()
    {
        return $this->approvalUrl;
    }

    public function getCaptureUrls()
    {
        return $this->captureUrls;
    }

    public function getAmount()
    {
        if (count($this->transactions) <= 0) {
            return null;
        }

        return $this->transactions[0]->getAmount();
    }
}