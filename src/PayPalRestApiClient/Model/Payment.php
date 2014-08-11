<?php

namespace PayPalRestApiClient\Model;

/**
 * The Payment class represents a paypal payment object
 */
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
    protected $captureUrl;

    /**
     * Construct 
     *
     * @param string $id not null
     * @param string $createTime not null
     * @param string $updateTime not null
     * @param string $state not null
     * @param string $intent not null
     * @param PayPalRestApiClient\Model\PayerInterface $payer not null
     * @param array $transactions not null
     * @param array $links not null
     *
     * @see https://developer.paypal.com/docs/api/#payments
     * @see https://developer.paypal.com/docs/api/#payment-object
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

    /**
     * Returns the approval url that should be use to redirect the user to the paypal website
     *
     * @return string
     */
    public function getApprovalUrl()
    {
        return $this->approvalUrl;
    }

    /**
     * Returns the capture url that should be use to capture an authorized payment
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->captureUrl;
    }

    /**
     * If set, returns the first transaction amount object
     *
     * N.B.: At the moment, the PayPal REST API do not support multiple transactions
     *
     * @return PayPalRestApiClient\Model\Amount|null
     */
    public function getAmount()
    {
        if (count($this->transactions) <= 0) {
            return null;
        }

        return $this->transactions[0]->getAmount();
    }
}