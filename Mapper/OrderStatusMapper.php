<?php

namespace SysPay\Payment\Mapper;

use SysPay\Payment\Model\Order\Source\Status as SysPayOrderStatusSource;
use SysPay\Payment\Gateway\Response\Data\Resolver as SysPayResponseResolver;
use Magento\Sales\Model\Order;

class OrderStatusMapper
{

    /**
     * Get the order status from the payment status
     *
     * @param string $sysPayPaymentStatus
     * @return string
     */
    public function getOrderStatusByPaymentStatus(string $sysPayPaymentStatus): string
    {
        switch ($sysPayPaymentStatus) {
            case SysPayResponseResolver::PAYMENT_STATUS_SUCCESS:
                return SysPayOrderStatusSource::SYSPAY_STATUS_SUCCESS_CODE;
            case SysPayResponseResolver::PAYMENT_STATUS_FAILED:
                return SysPayOrderStatusSource::SYSPAY_STATUS_FAILED_CODE;
            case SysPayResponseResolver::PAYMENT_STATUS_CANCELLED:
                return SysPayOrderStatusSource::SYSPAY_STATUS_CANCELLED_CODE;
            case SysPayResponseResolver::PAYMENT_STATUS_AUTHORIZED:
                return SysPayOrderStatusSource::SYSPAY_STATUS_AUTHORIZED_CODE;
            case SysPayResponseResolver::PAYMENT_STATUS_VOIDED:
                return SysPayOrderStatusSource::SYSPAY_STATUS_VOIDED_CODE;
            case SysPayResponseResolver::PAYMENT_STATUS_WAITING:
                return SysPayOrderStatusSource::SYSPAY_STATUS_WAITING_CODE;
            case SysPayResponseResolver::PAYMENT_STATUS_ERROR:
                return SysPayOrderStatusSource::SYSPAY_STATUS_ERROR_CODE;
            case SysPayResponseResolver::PAYMENT_STATUS_TIMED_OUT:
                return SysPayOrderStatusSource::SYSPAY_STATUS_TIMED_OUT_CODE;
            case SysPayResponseResolver::PAYMENT_STATUS_EXPIRED:
                return SysPayOrderStatusSource::SYSPAY_STATUS_EXPIRED_CODE;
            default:
                return SysPayOrderStatusSource::SYSPAY_STATUS_OPEN_CODE;
        }
    }

    /**
     * Get the order state from the payment status
     *
     * @param $sysPayPaymentStatus
     * @return string
     */
    public function getOrderStateByPaymentStatus($sysPayPaymentStatus): string
    {
        return $this->getOrderStateByOrderStatus($this->getOrderStatusByPaymentStatus($sysPayPaymentStatus));
    }

    /**
     * Get the order state from the syspay order status
     *
     * @param string $sysPayOrderStatus
     * @return string
     */
    public function getOrderStateByOrderStatus(string $sysPayOrderStatus): string
    {
        switch ($sysPayOrderStatus) {
            case SysPayOrderStatusSource::SYSPAY_STATUS_AUTHORIZED_CODE:
            case SysPayOrderStatusSource::SYSPAY_STATUS_SUCCESS_CODE:
            case SysPayOrderStatusSource::SYSPAY_STATUS_VOIDED_CODE:
                return Order::STATE_PROCESSING;
            case SysPayOrderStatusSource::SYSPAY_STATUS_CANCELLED_CODE:
            case SysPayOrderStatusSource::SYSPAY_STATUS_ERROR_CODE:
            case SysPayOrderStatusSource::SYSPAY_STATUS_TIMED_OUT_CODE:
            case SysPayOrderStatusSource::SYSPAY_STATUS_EXPIRED_CODE:
            case SysPayOrderStatusSource::SYSPAY_STATUS_FAILED_CODE:
            case SysPayOrderStatusSource::SYSPAY_STATUS_WAITING_CODE:
            case SysPayOrderStatusSource::SYSPAY_STATUS_OPEN_CODE:
            default:
                return Order::STATE_PAYMENT_REVIEW;
        }
    }
}
