<?php

namespace Omnipay\YooKassa;

interface ItemInterface extends \Omnipay\Common\ItemInterface
{
    public function getVatCode();

    public function getPaymentMode();

    public function getPaymentSubject();
}
