<?php

namespace Omnipay\YooKassa;

interface CustomerInterface
{
    public function getFullName();

    public function getPhone();

    public function getEmail();
}
