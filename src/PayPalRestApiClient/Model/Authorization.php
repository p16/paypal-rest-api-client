<?php

namespace PayPalRestApiClient\Model;

class Authorization
{
    protected $id;
    protected $createTime;
    protected $updateTime;
    protected $state;
    protected $intent;
    protected $payer;
    protected $transactions;
    protected $links;

    protected $captureUrl;

    /**
     * @see https://developer.paypal.com/docs/api/#amount-object
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

    private function initUrls()
    {
        $links = array_merge(
            $this->links,
            $this->transactions[0]['related_resources'][0]['authorization']['links']
        );

        foreach ($links as $link) {
            switch ($link['rel']) {
                case 'capture':
                    $this->captureUrl = $link['href'];
                    break;
            }
        }
    }

    public function getId()
    {
        return $this->id;
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

    public function getCaptureUrl()
    {
        return $this->captureUrl;
    }
}