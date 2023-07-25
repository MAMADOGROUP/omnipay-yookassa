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

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\ItemInterface;
use Throwable;

/**
 * Class PurchaseRequest.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'currency', 'returnUrl', 'transactionId', 'description', 'items', 'customer');

        return [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'return_url' => $this->getReturnUrl(),
            'transactionId' => $this->getTransactionId(),
            'items' => $this->getItems(),
            'customer' => $this->getCustomer(),
        ];
    }

    public function sendData($data)
    {
        try {
            $paymentResponse = $this->client->createPayment([
                'amount' => [
                    'value' => $data['amount'],
                    'currency' => $data['currency'],
                ],
                'description' => $data['description'],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => $data['return_url'],
                ],
                'metadata' => [
                    'transactionId' => $data['transactionId'],
                ],
                'receipt' => [
                    'customer' => $data['customer'],
                    'items' => array_map(function (ItemInterface $item) {
                        return [
                            'description' => $item->getDescription(),
                            'quantity' => $item->getQuantity(),
                            'amount' => [
                                'value' => round($item->getPrice(), 2),
                                'currency' => 'RUB',
                            ],
                            'vat_code' => $item->getVatCode(),
                            'payment_mode' => $item->getPaymentMode(),
                            'payment_subject' => $item->getPaymentSubject(),
                        ];
                    }, $data['items']->all()),
                ],
            ], $this->makeIdempotencyKey());

            return $this->response = new PurchaseResponse($this, $paymentResponse);
        } catch (Throwable $e) {
            throw new InvalidRequestException('Failed to request purchase: ' . $e->getMessage(), 0, $e);
        }
    }

    private function makeIdempotencyKey(): string
    {
        $data = $this->getData();
        if (isset($data['items'])) {
            $data['items'] = json_encode($data['items']);
        }
        if (isset($data['customer'])) {
            $data['customer'] = json_encode($data['customer']);
        }

        return md5(implode(',', array_merge(['create'], $data)));
    }
}
