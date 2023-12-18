<?php

namespace SysPay\Payment\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PaymentFlow implements OptionSourceInterface
{
    const SERVER_TO_SERVER = '1';
    const HOSTED_PAGE = '2';

    /**
     * Possible payment flows
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::SERVER_TO_SERVER,
                'label' => __('Server to server'),
            ],
            [
                'value' => self::HOSTED_PAGE,
                'label' => __('Hosted page'),
            ]
        ];
    }
}
