<?php

namespace SysPay\Payment\Plugin\Sales\Model\Order\Payment;

use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Payment;
use SysPay\Payment\Model\Order\Source\Status;

class ResolveOrderStatusAfterVoid
{
    /**
     * @param Payment $subject
     * @param $result
     * @param DataObject $document
     * @return mixed
     */
    public function afterVoid(Payment $subject, $result, DataObject $document)
    {
        $subject->getOrder()->setStatus(Status::SYSPAY_STATUS_VOIDED_CODE);
        return $result;
    }
}
