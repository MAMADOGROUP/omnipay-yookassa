<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use YooKassa\Model\Notification\NotificationFactory;
use YooKassa\Request\Payments\CreatePaymentResponse;

/**
 * @property CreatePaymentResponse $data
 */
class NotificationResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, (new NotificationFactory())->factory($data)->getObject());
    }

    public function isSuccessful()
    {
        return $this->getRequest()->isValid();
    }

    public function getTransactionReference()
    {
        return $this->data->getId();
    }

    public function getTransactionId()
    {
        return $this->data->getMetadata()['transactionId'] ?? null;
    }
}
