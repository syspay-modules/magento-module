<?php

namespace SysPay\Payment\Gateway\Command;

use SysPay\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Gateway\CommandInterface;

class DenyPaymentCommand implements CommandInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    private $orderRepository;

    /**
     * @param SubjectReader $subjectReader
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(SubjectReader $subjectReader, OrderRepositoryInterface $orderRepository)
    {
        $this->subjectReader = $subjectReader;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject)
    {

        $payment = $this->subjectReader->readPayment($commandSubject)->getPayment();
        $payment->setIsTransactionClosed(true);

        $order = $payment->getOrder();

        $order->addCommentToStatusHistory(__('Payment denied by Admin'));
        $order->setState(Order::STATE_CANCELED);
        $order->setStatus(Order::STATE_CANCELED);

        $this->orderRepository->save($order);
    }
}
