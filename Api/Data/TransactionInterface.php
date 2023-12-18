<?php

namespace SysPay\Payment\Api\Data;

interface TransactionInterface extends \Magento\Sales\Api\Data\TransactionInterface
{
    const TYPE_SALE = 'sale';
}
