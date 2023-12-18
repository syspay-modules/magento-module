<?php

namespace SysPay\Payment\Model\Order\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{

    const SYSPAY_STATUS_OPEN_CODE = 'syspay_open';
    const SYSPAY_STATUS_SUCCESS_CODE = 'syspay_success';
    const SYSPAY_STATUS_FAILED_CODE = 'syspay_failed';
    const SYSPAY_STATUS_CANCELLED_CODE = 'syspay_cancelled';
    const SYSPAY_STATUS_AUTHORIZED_CODE = 'syspay_authorized';
    const SYSPAY_STATUS_VOIDED_CODE = 'syspay_voided';
    const SYSPAY_STATUS_WAITING_CODE = 'syspay_waiting';
    const SYSPAY_STATUS_ERROR_CODE = 'syspay_error';
    const SYSPAY_STATUS_TIMED_OUT_CODE = 'syspay_timed_out';
    const SYSPAY_STATUS_EXPIRED_CODE = 'syspay_expired';


    const SYSPAY_STATUS_OPEN_LABEL = 'SysPay OPEN';
    const SYSPAY_STATUS_SUCCESS_LABEL = 'SysPay SUCCESS';
    const SYSPAY_STATUS_FAILED_LABEL = 'SysPay FAILED';
    const SYSPAY_STATUS_CANCELLED_LABEL = 'SysPay CANCELLED';
    const SYSPAY_STATUS_AUTHORIZED_LABEL = 'SysPay AUTHORIZED';
    const SYSPAY_STATUS_VOIDED_LABEL = 'SysPay VOIDED';
    const SYSPAY_STATUS_WAITING_LABEL = 'SysPay WAITING';
    const SYSPAY_STATUS_ERROR_LABEL = 'SysPay ERROR';
    const SYSPAY_STATUS_TIMED_OUT_LABEL = 'SysPay TIMED OUT';
    const SYSPAY_STATUS_EXPIRED_LABEL = 'SysPay EXPIRED';

    private ?array $options = null;

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_OPEN_LABEL,
                'value' => self::SYSPAY_STATUS_OPEN_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_SUCCESS_LABEL,
                'value' => self::SYSPAY_STATUS_SUCCESS_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_FAILED_LABEL,
                'value' => self::SYSPAY_STATUS_FAILED_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_CANCELLED_LABEL,
                'value' => self::SYSPAY_STATUS_CANCELLED_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_AUTHORIZED_LABEL,
                'value' => self::SYSPAY_STATUS_AUTHORIZED_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_VOIDED_LABEL,
                'value' => self::SYSPAY_STATUS_VOIDED_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_WAITING_LABEL,
                'value' => self::SYSPAY_STATUS_WAITING_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_ERROR_LABEL,
                'value' => self::SYSPAY_STATUS_ERROR_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_TIMED_OUT_LABEL,
                'value' => self::SYSPAY_STATUS_TIMED_OUT_CODE
            ];
            $this->options[] = [
                'label' => self::SYSPAY_STATUS_EXPIRED_LABEL,
                'value' => self::SYSPAY_STATUS_EXPIRED_CODE
            ];
        }

        return $this->options;
    }

    public function getOptionArray(): array
    {
        $options = [];
        foreach ($this->toOptionArray() as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }
}
