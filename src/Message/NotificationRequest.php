<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\NotificationInterface;
use YooKassa\Model\NotificationEventType;

class NotificationRequest extends AbstractRequest implements NotificationInterface
{
    public function isValid(): bool
    {
        $ranges = [
            '172.20.0.1/1', // TODO delete
            '185.71.76.0/27',
            '185.71.77.0/27',
            '77.75.153.0/25',
            '77.75.154.128/25',
        ];

        foreach ($ranges as $range) {
            if ($this->cidrMatch($this->httpRequest->getClientIp(), $range)) {
                return true;
            }
        }

        return false;
    }

    public function getData()
    {
        $body = $this->httpRequest->getContent();

        return json_decode($body, true);
    }

    public function getTransactionStatus()
    {
        $event = $this->getData()['event'];

        switch ($event) {
            case NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE:
                return NotificationInterface::STATUS_PENDING;
            case NotificationEventType::PAYMENT_SUCCEEDED:
                return NotificationInterface::STATUS_COMPLETED;
            case NotificationEventType::PAYMENT_CANCELED:
            case NotificationEventType::REFUND_SUCCEEDED:
            default:
                return $event;
        }
    }

    public function getMessage()
    {
        return null;
    }

    public function sendData($data)
    {
        try {
            return $this->response = new NotificationResponse($this, $data);
        } catch (\Throwable $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    private function cidrMatch($ip, $range)
    {
        list($subnet, $bits) = explode('/', $range);

        if ($bits === null) {
            $bits = 32;
        }

        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask; // nb: in case the supplied subnet wasn't correctly aligned

        return ($ip & $mask) == $subnet;
    }
}
