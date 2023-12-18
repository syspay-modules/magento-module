<?php

namespace SysPay\Payment\Exception;

use Magento\Payment\Model\InfoInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Exception\LocalizedException;

class SysPayEmsCanNotUpdatePaymentException extends LocalizedException
{
    private InfoInterface $payment;

    public function __construct(Phrase $phrase, InfoInterface $payment, \Exception $cause = null, $code = 0)
    {
        $this->payment = $payment;
        parent::__construct($phrase, $cause, $code);
    }

    /**
     * @return InfoInterface
     */
    public function getPayment(): InfoInterface
    {
        return $this->payment;
    }
}
