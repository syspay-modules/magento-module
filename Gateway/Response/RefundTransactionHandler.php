<?php

namespace SysPay\Payment\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use SysPay\Payment\Gateway\Response\Data\Resolver;

class RefundTransactionHandler extends GeneralTransactionHandler
{

    private const REFUND_STATUS_SUCCESS = 'SUCCESS';
    private const REFUND_STATUS_FAILED = 'FAILED';
    private const REFUND_STATUS_ERROR = 'ERROR';

    /**
     * @param array $handlingSubject
     * @param Resolver $response
     * @return void
     * @throws LocalizedException
     */
    protected function handleTransaction(array $handlingSubject, Resolver $response): void
    {
        $responseRefundStatus = $response->getRefund()->getStatus();

        if ($responseRefundStatus != self::REFUND_STATUS_SUCCESS) {
            switch ($responseRefundStatus) {
                case self::REFUND_STATUS_FAILED:
                    throw new LocalizedException(__('Cannot refund transaction. The refund has been refused.'));
                case self::REFUND_STATUS_ERROR:
                    throw new LocalizedException(__('Cannot refund transaction. An error occured while processing the refund.'));
                default:
                    throw new LocalizedException(__('Cannot refund transaction. Refund status is not success.'));
            }
        }
    }
}
