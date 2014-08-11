<?php

namespace PayPalRestApiClient\Model;

/**
 * The PaymentAuthorization class represents a paypal payment authorization object
 */
class PaymentAuthorization
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

    protected $captureUrls = array();

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
     *
     * @see https://developer.paypal.com/docs/integration/direct/capture-payment/#authorize-the-payment
     * 
     * N.B.: the documentation links to this authorization object
     * https://developer.paypal.com/docs/api/#authorization-object but actually returns the one
     * described here https://developer.paypal.com/docs/integration/direct/capture-payment/#authorize-the-payment
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
                    $this->captureUrls[] = $link['href'];
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

    /**
     * Retrun the urls to call when capturing the authorized payments
     *
     * @return array
     */
    public function getCaptureUrls()
    {
        return $this->captureUrls;
    }
}