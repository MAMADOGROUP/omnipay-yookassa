<?php

namespace Omnipay\YooKassa;

interface CustomerInterface
{
    public function getYooKassaFullName();

    public function getYooKassaPhone();

    public function getYooKassaEmail();
}
