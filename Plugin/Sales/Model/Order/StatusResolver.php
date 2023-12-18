<?php

namespace SysPay\Payment\Plugin\Sales\Model\Order;

use SysPay\Payment\Model\Ui\ConfigProvider;
use SysPay\Payment\Mapper\OrderStatusMapper;
use Magento\Sales\Model\Order\StatusResolver as Subject;
use Magento\Sales\Api\Data\OrderInterface;

class StatusResolver
{

    private OrderStatusMapper $orderStatusMapper;

    public function __construct(OrderStatusMapper $orderStatusMapper)
    {
        $this->orderStatusMapper = $orderStatusMapper;
    }

    /**
     * @param Subject $subject
     * @param callable $proceed
     * @param OrderInterface $order
     * @param $state
     * @return string
     */
    public function aroundGetOrderStatusByState(Subject $subject, callable $proceed, OrderInterface $order, $state): string
    {

        if ($order->getPayment()->getMethod() === ConfigProvider::CODE) {
            $payment = $order->getPayment();
            $paymentStatus = $payment->getAdditionalInformation('status');
            if (is_string($paymentStatus)) {
                return $this->orderStatusMapper->getOrderStatusByPaymentStatus($paymentStatus);
            }
        }
        return $proceed($order, $state);
    }
}
