<?php

namespace SysPay\Payment\Block\Checkout;

use Magento\Checkout\Block\Onepage\Success;
use Magento\Sales\Api\Data\OrderInterface;

class PaymentFail extends Success
{
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('syspay.checkout.payment.fail.additional.info');
    }

    public function getPaymentFailureMessage(): string
    {
        $failureMessage = $this->getOrder()->getPayment()->getAdditionalInformation('failure_message');
        return is_string($failureMessage) ? $failureMessage : '';
    }

    public function getOrder(): OrderInterface
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
}
