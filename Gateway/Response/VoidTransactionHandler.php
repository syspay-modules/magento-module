<?php

namespace SysPay\Payment\Gateway\Response;

use SysPay\Payment\Gateway\Response\Data\Resolver;
use Magento\Framework\Exception\LocalizedException;

class VoidTransactionHandler extends GeneralTransactionHandler
{
    /**
     * @param array $handlingSubject
     * @param Resolver $response
     * @return void
     * @throws LocalizedException
     */
    protected function handleTransaction(array $handlingSubject, Resolver $response): void
    {
        if ($response->getPayment()->getStatus() != Resolver::PAYMENT_STATUS_VOIDED) {
            throw new LocalizedException(__('Cannot void transaction. Payment status is not voided.'));
        }
    }
}
