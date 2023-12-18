<?php

namespace SysPay\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;

class Data extends AbstractHelper
{

    const PLACED_ORDER_SUCCESS_REDIRECT_PATH = 'checkout/onepage/success';
    const PLACED_ORDER_FAIL_REDIRECT_PATH = 'syspay/checkout/paymentfail';

    /**
     * @param Order $order
     * @return bool
     */
    public function isOrderPlacedSuccess(Order $order): bool
    {
        return $order->getState() === Order::STATE_PROCESSING;
    }

    /**
     * @param Order $order
     * @return string
     */
    public function getPlacedOrderRedirectPath(Order $order): string
    {
        return $this->isOrderPlacedSuccess($order)
            ? static::PLACED_ORDER_SUCCESS_REDIRECT_PATH : static::PLACED_ORDER_FAIL_REDIRECT_PATH;
    }

}
