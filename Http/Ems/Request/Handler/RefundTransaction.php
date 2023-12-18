<?php

namespace SysPay\Payment\Http\Ems\Request\Handler;

use Magento\Framework\Model\AbstractModel;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order;
use SysPay\Payment\Exception\SysPayEmsCanNotUpdatePaymentException;
use SysPay\Payment\Gateway\Response\Data\Resolver as DataResolver;
use SysPay\Payment\Gateway\Response\GeneralTransactionHandler;

class RefundTransaction extends PaymentTransaction
{

    /**
     * @param DataResolver $requestDO
     * @return GeneralTransactionHandler
     */
    protected function getTransactionHandler(DataResolver $requestDO): GeneralTransactionHandler
    {
        return $this->transactionHandler;
    }

    /**
     * @param DataResolver $requestDO
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    protected function resolve(DataResolver $requestDO): array
    {
        $paymentDO = $this->getPaymentDO($requestDO);
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();

        if ($order->getState() === Order::STATE_CLOSED) {
            throw new SysPayEmsCanNotUpdatePaymentException(__('Order is closed'), $payment);
        }

        $transaction = $this->getTransaction($payment, $requestDO);
        $this->handleTransaction($paymentDO, $requestDO);


        return ['payment' => $payment, 'order' => $order, 'transaction' => $transaction];
    }

    /**
     * @param InfoInterface $payment
     * @param DataResolver $requestDO
     * @return AbstractModel
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function getTransaction(InfoInterface $payment, DataResolver $requestDO): AbstractModel
    {
        $reference = $requestDO->getReference();
        return $this->transactionRepository->getByTransactionId(
            $reference,
            $payment->getId(),
            $payment->getOrder()->getId()
        );
    }
}
