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
use YooKassa\Client;

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

    public function setYooKassaClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @param PaymentInterface $payment
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function injectPayment(PaymentInterface $payment)
    {
        $this
            ->setPayment($payment)
            ->setAmount($payment->getAmount())
            ->setCurrency($payment->getCurrency())
            ->setDescription($payment->getDescription())
            ->setReturnUrl($payment->getReturnUrl())
            ->setTransactionId($payment->getTransactionId())
            ->setTransactionReference($payment->getTransactionReference())
            ->setTransfers($payment->getTransfers());

        return $this;
    }
}
