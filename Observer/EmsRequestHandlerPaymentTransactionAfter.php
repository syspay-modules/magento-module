<?php

namespace SysPay\Payment\Observer;

use SysPay\Payment\Gateway\Config\Config;
use SysPay\Payment\Model\Order\Source\Status as SysPayOrderStatusSource;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Payment\Model\MethodInterface;

class EmsRequestHandlerPaymentTransactionAfter implements ObserverInterface
{
    private OrderSender $orderSender;
    private OrderRepositoryInterface $orderRepository;
    private InvoiceRepositoryInterface $invoiceRepository;
    private Config $config;

    public function __construct(
        OrderSender                $orderSender,
        OrderRepositoryInterface   $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        Config                     $config
    )
    {
        $this->orderSender = $orderSender;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->config = $config;
    }

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        if (!$order->getEmailSent()) {
            $this->orderSender->send($order);
            $order->setEmailSent(true);
            $this->orderRepository->save($order);
        }

        $this->updateInvoice($order);
    }

    /**
     * @param Order $order
     * @return void
     */
    protected function updateInvoice(Order $order): void
    {
        $isInvoiceShouldBeUpdated = $this->config->isHostedPageFlow()
            && $this->config->getPaymentAction() === MethodInterface::ACTION_AUTHORIZE_CAPTURE
            && $order->getStatus() === SysPayOrderStatusSource::SYSPAY_STATUS_SUCCESS_CODE;

        if ($isInvoiceShouldBeUpdated) {
            /** @var Order\Invoice $invoice */
            $invoice = $order->getInvoiceCollection()->getFirstItem();
            if ($invoice->getId() && (int)$invoice->getState() === Order\Invoice::STATE_OPEN) {
                $invoice->setTransactionId($order->getPayment()->getLastTransId());
                $invoice->setState(Order\Invoice::STATE_PAID);
                $this->invoiceRepository->save($invoice);
            }
        }
    }
}
