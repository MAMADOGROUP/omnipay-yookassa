<?php
/**
 * YooKassa driver for Omnipay payment processing library
 *
 * @link      https://github.com/igor-tv/omnipay-yookassa
 * @package   omnipay-yookassa
 * @license   MIT
 * @copyright Copyright (c) 2021, Igor Tverdokhleb, igor-tv@mail.ru
 */

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\PaymentInterface;
use YooKassaCheckout\Client;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\CustomerInterface;

/**
 * Class AbstractRequest.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var Client
     */
    protected $client;

    public function getShopId()
    {
        return $this->getParameter('shopId');
    }

    public function setShopId($value)
    {
        return $this->setParameter('shopId', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getTransfers()
    {
        return $this->getParameter('transfers');
    }

    public function setTransfers($value)
    {
        return $this->setParameter('transfers', $value);
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->getParameter('customer');
    }

    public function setCustomer($value)
    {
        if (!$value instanceof CustomerInterface) {
            throw new InvalidRequestException('Only CustomerInterface is supported');
        }

        return $this->setParameter('customer', $value);
    }

    public function getPayment(): PaymentInterface
    {
        return $this->getParameter('payment');
    }

    public function setPayment($payment)
    {
        if (!$payment instanceof PaymentInterface) {
            throw new InvalidRequestException('Only PaymentInterface is supported');
        }

        $this
            ->setAmount($payment->getAmount())
            ->setCurrency($payment->getCurrency())
            ->setDescription($payment->getDescription())
            ->setReturnUrl($payment->getReturnUrl())
            ->setTransactionId($payment->getTransactionId())
            ->setTransactionReference($payment->getTransactionReference())
            ->setItems($payment->getItems())
            ->setCustomer($payment->getCustomer());

        return $this->setParameter('payment', $payment);
    }

    public function setYooKassaClient(Client $client): void
    {
        $this->client = $client;
    }
}
